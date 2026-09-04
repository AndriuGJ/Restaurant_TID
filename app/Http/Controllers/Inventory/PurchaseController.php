<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StorePurchaseRequest;
use App\Models\Configuration\DocumentType;
use App\Models\Inventory\Product;
use App\Models\Inventory\Supplier;
use App\Models\Kardex\KardexMovement;
use App\Models\Purchases\Purchase;
use App\Models\Purchases\PurchaseDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $purchases = Purchase::with(['supplier', 'documentType'])
            ->latest('id')
            ->paginate(15);

        return view('inventory.purchases.index', compact('purchases'));
    }

    public function create(): View
    {
        return view('inventory.purchases.create', $this->formOptions());
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $purchase = Purchase::create([
                'supplier_id' => $request->input('supplier_id'),
                'user_id' => $request->user()->id,
                'purchase_type' => $request->input('purchase_type'),
                'document_type_id' => $request->input('document_type_id'),
                'series' => $request->input('series'),
                'number' => $request->input('number'),
                'purchase_date' => $request->input('purchase_date'),
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
                'status' => 'completed',
            ]);

            $this->processDetails($purchase, $request->input('details', []));
        });

        Cache::forget('inventory_purchases');
        Cache::forget('inventory_kardex');

        return redirect()->route('inventory.purchases.index')
            ->with('success', 'Compra registrada correctamente.');
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'documentType', 'user', 'details.product']);

        return view('inventory.purchases.show', compact('purchase'));
    }

    /**
     * @param  array<array{product_id?: int, quantity?: mixed, unit_price?: mixed}>  $details
     */
    private function processDetails(Purchase $purchase, array $details): void
    {
        $subtotal = 0;

        foreach ($details as $row) {
            $product = Product::findOrFail($row['product_id']);
            $lineSubtotal = $row['quantity'] * $row['unit_price'];

            PurchaseDetail::create([
                'purchase_id' => $purchase->id,
                'product_id' => $product->id,
                'quantity' => $row['quantity'],
                'unit_price' => $row['unit_price'],
                'subtotal' => $lineSubtotal,
            ]);

            $subtotal += $lineSubtotal;

            $this->applyPurchaseMovement($product, $row['quantity'], $purchase);
        }

        $purchase->update([
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);
    }

    private function applyPurchaseMovement(Product $product, float $quantity, Purchase $purchase): void
    {
        $product->stock += $quantity;
        $product->save();

        KardexMovement::create([
            'product_id' => $product->id,
            'movement_type' => 'purchase',
            'quantity_in' => $quantity,
            'quantity_out' => 0,
            'balance' => $product->stock,
            'related_document_type' => Purchase::class,
            'related_document_id' => $purchase->id,
        ]);
    }

    /**
     * @return array{suppliers: Collection, documentTypes: Collection, supplies: Collection}
     */
    private function formOptions(): array
    {
        return [
            'suppliers' => Supplier::where('status', true)->orderBy('social_reason')->get(['id', 'social_reason']),
            'documentTypes' => DocumentType::where('type', 'invoice')->where('status', true)->orderBy('name')->get(['id', 'name']),
            'supplies' => Product::where('type', 'supply')->where('status', true)->orderBy('name')->get(['id', 'name', 'unit_of_measure']),
        ];
    }
}
