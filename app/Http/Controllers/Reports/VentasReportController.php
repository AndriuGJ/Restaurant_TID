<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Configuration\DocumentType;
use App\Models\Sales\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VentasReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $saleType = $request->input('sale_type');
        $documentTypeId = $request->input('document_type_id');

        $sales = Sale::with(['clientable', 'documentType', 'cashRegisterSession'])
            ->where('status', 'paid')
            ->when($from, fn ($query) => $query->whereDate('updated_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('updated_at', '<=', $to))
            ->when($saleType, fn ($query) => $query->where('sale_type', $saleType))
            ->when($documentTypeId, fn ($query) => $query->where('document_type_id', $documentTypeId))
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        $summary = Sale::where('status', 'paid')
            ->when($from, fn ($query) => $query->whereDate('updated_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('updated_at', '<=', $to))
            ->when($saleType, fn ($query) => $query->where('sale_type', $saleType))
            ->when($documentTypeId, fn ($query) => $query->where('document_type_id', $documentTypeId))
            ->selectRaw('COUNT(*) as total_count, COALESCE(SUM(total), 0) as total_amount')
            ->first();

        $documentTypes = DocumentType::orderBy('name')->get(['id', 'name']);

        return view('reports.ventas', compact('sales', 'summary', 'from', 'to', 'saleType', 'documentTypeId', 'documentTypes'));
    }
}
