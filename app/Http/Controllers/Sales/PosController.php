<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\AddProductRequest;
use App\Http\Requests\Sales\DeliveryInfoRequest;
use App\Http\Requests\Sales\MoveTableRequest;
use App\Http\Requests\Sales\PaySaleRequest;
use App\Models\Configuration\Company;
use App\Models\Configuration\DocumentType;
use App\Models\Configuration\PaymentMethod;
use App\Models\Configuration\SunatConfig;
use App\Models\Customers\CompanyClient;
use App\Models\Customers\Customer;
use App\Models\Inventory\Product;
use App\Models\Inventory\ProductCategory;
use App\Models\Kardex\KardexMovement;
use App\Models\Restaurant\CashRegisterSession;
use App\Models\Restaurant\DeliveryProvider;
use App\Models\Restaurant\Hall;
use App\Models\Restaurant\Table;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use App\Models\Sales\SalePayment;
use Greenter\Model\Client\Client as SunatClient;
use Greenter\Model\Company\Address as SunatAddress;
use Greenter\Model\Company\Company as SunatCompany;
use Greenter\Model\Sale\Invoice as SunatInvoice;
use Greenter\Model\Sale\Legend as SunatLegend;
use Greenter\Model\Sale\SaleDetail as SunatSaleDetail;
use Greenter\Xml\Builder\InvoiceBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PosController extends Controller
{
    public function hall(Request $request): View
    {
        $halls = Hall::with(['tables' => fn ($q) => $q->orderBy('name')])->where('status', true)->get();

        $activeHall = $halls->firstWhere('id', $request->integer('hall')) ?? $halls->first();
        $this->arrangeAutoTables($activeHall?->tables ?? collect());

        return view('pos.hall', compact('halls', 'activeHall'));
    }

    public function requiresSession(): View
    {
        return view('pos.requires-session');
    }

    public function moveTable(MoveTableRequest $request, Table $table): JsonResponse
    {
        $table->update($request->validated());

        return response()->json(['ok' => true]);
    }

    public function openSale(Table $table): RedirectResponse
    {
        $existing = $this->openSaleForTable($table);

        return redirect()->route('pos.sale', $existing);
    }

    public function newDelivery(): RedirectResponse
    {
        $sale = Sale::create([
            'user_id' => auth()->id(),
            'cash_register_session_id' => $this->activeCashRegisterSession()?->id,
            'guests' => 1,
            'sale_type' => 'delivery',
            'is_takeaway' => true,
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
            'status' => 'pending',
        ]);

        return redirect()->route('pos.sale', $sale);
    }

    public function newQuickSale(): RedirectResponse
    {
        $sale = Sale::create([
            'user_id' => auth()->id(),
            'cash_register_session_id' => $this->activeCashRegisterSession()?->id,
            'guests' => 1,
            'sale_type' => 'quick_sale',
            'is_takeaway' => true,
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
            'status' => 'pending',
        ]);

        return redirect()->route('pos.sale', $sale);
    }

    public function saveDeliveryInfo(DeliveryInfoRequest $request, Sale $sale): RedirectResponse
    {
        $sale->update($request->validated());

        return redirect()->back()->with('success', 'Información de delivery actualizada.');
    }

    public function sale(Sale $sale): View
    {
        $sale->load(['table', 'details.product', 'deliveryProvider']);

        $categories = ProductCategory::where('status', true)->with('products', fn ($q) => $q->where('status', true)->where('is_pos_item', true))->orderBy('name')->get();
        $products = Product::where('status', true)->where('is_pos_item', true)->orderBy('name')->get(['id', 'name', 'sale_price', 'product_category_id', 'image_url']);

        $deliveryProviders = DeliveryProvider::where('status', true)->orderBy('name')->get(['id', 'name']);
        $sale->loadCount('details');

        return view('pos.sale', compact('sale', 'categories', 'products', 'deliveryProviders'));
    }

    public function addProduct(AddProductRequest $request, Sale $sale): RedirectResponse
    {
        $product = Product::findOrFail($request->input('product_id'));

        DB::transaction(function () use ($request, $sale, $product) {
            $lineSubtotal = $request->input('quantity') * (float) $product->sale_price;

            SaleDetail::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'quantity' => $request->input('quantity'),
                'unit_price' => $product->sale_price,
                'subtotal' => $lineSubtotal,
                'notes' => $request->input('notes'),
                'kitchen_status' => 'pending',
            ]);

            $this->updateSaleTotals($sale);
        });

        Cache::forget("sale_{$sale->id}");

        return redirect()->back()->with('success', 'Producto agregado al pedido.');
    }

    public function updateDetailQuantity(Request $request, SaleDetail $detail): RedirectResponse
    {
        $request->validate(['quantity' => ['required', 'numeric', 'gt:0']]);

        $detail->update([
            'quantity' => $request->input('quantity'),
            'subtotal' => $request->input('quantity') * (float) $detail->unit_price,
        ]);

        $this->updateSaleTotals($detail->sale);

        return redirect()->back();
    }

    public function removeDetail(SaleDetail $detail): RedirectResponse
    {
        $sale = $detail->sale;
        $detail->delete();
        $this->updateSaleTotals($sale);

        if ($sale->details()->count() === 0) {
            $sale->update(['status' => 'pending']);
        }

        return redirect()->back();
    }

    public function sendToKitchen(Sale $sale): RedirectResponse
    {
        if ($sale->details()->where('kitchen_status', 'pending')->exists()) {
            $sale->update(['status' => 'preparing']);
        }

        return redirect()->route('pos.sale', $sale)->with('success', 'Pedido enviado a cocina.');
    }

    public function checkout(Sale $sale): View
    {
        $sale->load(['details.product', 'table']);

        $documentTypes = DocumentType::where('type', 'invoice')->where('status', true)->orderBy('name')->get(['id', 'name', 'nomenclature']);
        $paymentMethods = PaymentMethod::where('status', true)->orderBy('name')->get(['id', 'name']);

        return view('pos.checkout', compact('sale', 'documentTypes', 'paymentMethods'));
    }

    public function pay(PaySaleRequest $request, Sale $sale): RedirectResponse
    {
        DB::transaction(function () use ($request, $sale) {
            $client = null;
            if ($request->input('clientable_type') === 'customer') {
                $client = Customer::find($request->input('clientable_id'));
            } elseif ($request->input('clientable_type') === 'company') {
                $client = CompanyClient::find($request->input('clientable_id'));
            }

            $payout = collect($request->input('payments'))->sum('amount');
            $total = (float) $sale->total;

            $sale->update([
                'clientable_type' => $client ? $client->getMorphClass() : null,
                'clientable_id' => $client?->id,
                'document_type_id' => $request->input('document_type_id'),
                'series' => $this->seriesForSale($request, $sale),
                'number' => $this->numberForSale($request, $sale),
                'guests' => $request->input('guests', $sale->guests),
                'notes' => $request->input('notes'),
                'change' => max($payout - $total, 0),
                'status' => 'paid',
            ]);

            foreach ($request->input('payments') as $payment) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'payment_method_id' => $payment['payment_method_id'],
                    'amount' => $payment['amount'],
                ]);
            }

            $this->applyStockKardex($sale);
            $this->releaseTable($sale);
        });

        Cache::forget("sale_{$sale->id}");

        $sale->load('documentType');

        $isDocument = $sale->document_type_id !== null
            && $sale->documentType?->type === 'invoice';

        return $isDocument && $sale->series && $sale->number
            ? redirect()->route('pos.sale.receipt', $sale)->with('success', 'Venta pagada y comprobante emitido.')
            : redirect()->route('pos.hall')->with('success', 'Venta pagada correctamente.');
    }

    public function receipt(Sale $sale): View
    {
        abort_unless(
            $sale->status === 'paid' && $sale->series && $sale->number,
            404
        );

        $sale->load(['details.product', 'clientable', 'documentType', 'payments.paymentMethod']);
        $company = $this->emittingCompany();

        $whatsappNumber = preg_replace('/[^0-9]/', '', (string) $company->phone);
        $message = "Comprobante {$sale->series}-{$sale->number} del pedido #{$sale->id} por S/ {$sale->total}.";
        $whatsappUrl = $whatsappNumber
            ? "https://wa.me/{$whatsappNumber}?text=".urlencode($message)
            : null;

        return view('pos.receipt', compact('sale', 'company', 'whatsappUrl'));
    }

    public function receiptXml(Sale $sale): Response
    {
        abort_unless(
            $sale->status === 'paid' && $sale->series && $sale->number,
            404
        );

        $xml = $this->buildInvoiceXml($sale);
        $name = "{$sale->series}-{$sale->number}";

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => "attachment; filename=\"{$name}.xml\"",
        ]);
    }

    public function cancel(Sale $sale): RedirectResponse
    {
        $sale->update(['status' => 'cancelled']);
        $this->releaseTable($sale);

        return redirect()->route('pos.hall')->with('success', 'Venta cancelada.');
    }

    private function openSaleForTable(Table $table): Sale
    {
        $existing = $table->sales()->whereIn('status', ['pending', 'preparing'])->latest('id')->first();

        if ($existing) {
            return $existing;
        }

        $sale = Sale::create([
            'user_id' => auth()->id(),
            'cash_register_session_id' => $this->activeCashRegisterSession()?->id,
            'table_id' => $table->id,
            'guests' => 1,
            'sale_type' => 'pos',
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
            'status' => 'pending',
        ]);

        $table->update(['status' => 'occupied']);

        return $sale;
    }

    private function updateSaleTotals(Sale $sale): void
    {
        $subtotal = (float) $sale->details()->sum('subtotal');
        $sale->update([
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $subtotal,
        ]);
    }

    private function applyStockKardex(Sale $sale): void
    {
        foreach ($sale->details as $detail) {
            $product = $detail->product;

            if ($product->type === 'supply') {
                $product->stock -= $detail->quantity;
                $product->save();

                KardexMovement::create([
                    'product_id' => $product->id,
                    'movement_type' => 'sale',
                    'quantity_in' => 0,
                    'quantity_out' => $detail->quantity,
                    'balance' => $product->stock,
                    'related_document_type' => Sale::class,
                    'related_document_id' => $sale->id,
                ]);
            }
        }
    }

    private function releaseTable(Sale $sale): void
    {
        if ($sale->table_id) {
            $sale->table()->update(['status' => 'available']);
        }
    }

    private function arrangeAutoTables(Collection $tables): void
    {
        $col = 0;
        $perRow = 5;
        $spacingX = 160;
        $spacingY = 150;
        $padding = 40;

        foreach ($tables as $table) {
            if ($table->pos_x === null) {
                $table->update([
                    'pos_x' => $padding + ($col % $perRow) * $spacingX,
                    'pos_y' => $padding + intdiv($col, $perRow) * $spacingY,
                ]);
                $col++;
            }
        }
    }

    private function seriesForSale(PaySaleRequest $request, Sale $sale): ?string
    {
        $documentType = $this->invoiceDocumentType($request);

        if (! $documentType) {
            return null;
        }

        return ($documentType->nomenclature ?: 'F').'001';
    }

    private function numberForSale(PaySaleRequest $request, Sale $sale): ?string
    {
        $documentType = $this->invoiceDocumentType($request);

        if (! $documentType) {
            return null;
        }

        $config = $this->activeSunatConfig();
        $sequence = $config ? $config->used_receipts + 1 : 1;

        if ($config) {
            $config->increment('used_receipts');
        }

        return str_pad((string) $sequence, 8, '0', STR_PAD_LEFT);
    }

    private function activeSunatConfig(): ?SunatConfig
    {
        return SunatConfig::where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->whereColumn('used_receipts', '<', 'max_receipts')
            ->orderBy('id')
            ->first();
    }

    private function activeCashRegisterSession(): ?CashRegisterSession
    {
        return CashRegisterSession::where('status', 'open')
            ->latest('id')
            ->first();
    }

    private function invoiceDocumentType(PaySaleRequest $request): ?DocumentType
    {
        $documentId = $request->input('document_type_id');

        if (! $documentId) {
            return null;
        }

        $documentType = DocumentType::find($documentId);

        return $documentType && $documentType->type === 'invoice' ? $documentType : null;
    }

    private function buildInvoiceXml(Sale $sale): string
    {
        $sale->load(['details.product', 'clientable', 'documentType']);

        $company = $this->emittingCompany();
        $customer = $sale->clientable;
        $documentType = $sale->documentType;
        $isBoleta = $documentType?->nomenclature !== null
            && mb_strtoupper($documentType->nomenclature) === 'B';

        $sunatCompany = (new SunatCompany)
            ->setRuc($company->ruc)
            ->setRazonSocial($company->social_reason)
            ->setNombreComercial($company->commercial_name ?: $company->name)
            ->setAddress((new SunatAddress)
                ->setUbigueo($company->ubigeo?->code ?: '')
                ->setDepartamento($company->ubigeo?->department ?: '')
                ->setProvincia($company->ubigeo?->province ?: '')
                ->setDistrito($company->ubigeo?->district ?: '')
                ->setDireccion($company->fiscal_address ?: ''));

        $clientNumDoc = $customer?->document_number ?: $customer?->ruc;
        $clientRzn = $customer?->social_reason ?: $customer?->name;

        $sunatClient = (new SunatClient)
            ->setTipoDoc($isBoleta ? '1' : ($documentType->nomenclature === 'RUC' ? '6' : '1'))
            ->setNumDoc($isBoleta ? ($clientNumDoc ?: '00000000') : $clientNumDoc)
            ->setRznSocial($isBoleta ? ($clientRzn ?: 'PÚBLICO GENERAL') : $clientRzn);

        $details = $sale->details->map(function (SaleDetail $detail) {
            return (new SunatSaleDetail)
                ->setUnidad('NIU')
                ->setCantidad((float) $detail->quantity)
                ->setDescripcion($detail->product?->name ?: 'Producto')
                ->setMtoValorUnitario((float) $detail->unit_price)
                ->setMtoPrecioUnitario((float) $detail->unit_price)
                ->setTipAfeIgv('10')
                ->setMtoValorVenta((float) $detail->subtotal);
        })->all();

        $subtotal = (float) $sale->subtotal;
        $igv = round($subtotal * 0.18, 2);
        $total = (float) $sale->total;

        $invoice = (new SunatInvoice)
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101')
            ->setTipoDoc($isBoleta ? '03' : '01')
            ->setSerie($sale->series)
            ->setCorrelativo($sale->number)
            ->setFechaEmision(now())
            ->setCompany($sunatCompany)
            ->setClient($sunatClient)
            ->setMtoOperGravadas($subtotal)
            ->setMtoIGV($igv)
            ->setTotalImpuestos($igv)
            ->setMtoImpVenta($total)
            ->setTipoMoneda('PEN')
            ->setDetails($details)
            ->setLegends([
                (new SunatLegend)
                    ->setCode('1000')
                    ->setValue(strtoupper('SON '.number_format($total, 2, '.', '').' SOLES')),
            ]);

        return (new InvoiceBuilder)->build($invoice);
    }

    private function emittingCompany(): Company
    {
        return Company::query()
            ->whereHas('sunatConfigs', fn ($q) => $q->where('status', 'active'))
            ->orderBy('id')
            ->first()
            ?? Company::query()->first()
            ?? new Company;
    }
}
