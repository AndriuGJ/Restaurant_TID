<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Configuration\Company;
use App\Models\Configuration\DocumentType;
use App\Models\Sales\Sale;
use App\Services\TcpdfService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class VentasReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        $sales = $this->querySales($filters)
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        $summary = $this->querySales($filters)
            ->selectRaw('COUNT(*) as total_count, COALESCE(SUM(total), 0) as total_amount')
            ->first();

        $documentTypes = DocumentType::where('type', 'invoice')->orderBy('name')->get(['id', 'name']);

        return view('reports.ventas', array_merge($filters, [
            'sales' => $sales,
            'summary' => $summary,
            'documentTypes' => $documentTypes,
        ]));
    }

    public function preview(Request $request, TcpdfService $tcpdf): Response
    {
        return response($this->buildPdf($request, $tcpdf), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function export(Request $request, TcpdfService $tcpdf): Response
    {
        $filename = 'reporte_ventas_'.now()->format('Y-m-d_His').'.pdf';

        return response($this->buildPdf($request, $tcpdf), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function buildPdf(Request $request, TcpdfService $tcpdf): string
    {
        return $tcpdf->render('reports.ventas.pdf', $this->pdfPayload($request));
    }

    /**
     * @return array<string, mixed>
     */
    private function pdfPayload(Request $request): array
    {
        $filters = $this->filters($request);
        $sales = $this->querySales($filters)->orderByDesc('updated_at')->get();
        $summary = $this->querySales($filters)
            ->selectRaw('COUNT(*) as total_count, COALESCE(SUM(total), 0) as total_amount')
            ->first();

        $saleTypeLabels = ['pos' => 'Salón', 'delivery' => 'Delivery', 'quick_sale' => 'Venta rápida'];

        $documentTypeName = null;
        if ($filters['documentTypeId']) {
            $documentTypeName = DocumentType::whereKey($filters['documentTypeId'])->value('name');
        }

        $company = Company::first();
        $tcpdf = app(TcpdfService::class);

        return [
            'company' => $company,
            'logoDataUri' => $tcpdf->logoDataUri($company),
            'sales' => $sales,
            'summary' => $summary,
            'from' => $filters['from'],
            'to' => $filters['to'],
            'saleType' => $filters['saleType'],
            'saleTypeLabel' => $saleTypeLabels[$filters['saleType'] ?? ''] ?? null,
            'documentTypeName' => $documentTypeName,
            'title' => 'Reporte de Ventas',
            'generatedAt' => now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y, h:i a'),
        ];
    }

    /**
     * Extract the report filters from the request.
     *
     * @return array<string, mixed>
     */
    private function filters(Request $request): array
    {
        return [
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'saleType' => $request->input('sale_type'),
            'documentTypeId' => $request->input('document_type_id'),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function querySales(array $filters)
    {
        return Sale::with(['clientable', 'documentType', 'cashRegisterSession'])
            ->where('status', 'paid')
            ->when($filters['from'], fn ($query) => $query->whereDate('updated_at', '>=', $filters['from']))
            ->when($filters['to'], fn ($query) => $query->whereDate('updated_at', '<=', $filters['to']))
            ->when($filters['saleType'], fn ($query) => $query->where('sale_type', $filters['saleType']))
            ->when($filters['documentTypeId'], fn ($query) => $query->where('document_type_id', $filters['documentTypeId']));
    }
}
