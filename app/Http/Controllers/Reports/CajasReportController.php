<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\CashRegisterSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CajasReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $sessions = CashRegisterSession::with(['cashRegister', 'shift', 'userOpening', 'userClosing'])
            ->when($from, fn ($query) => $query->whereDate('opened_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('opened_at', '<=', $to))
            ->withCount('sales')
            ->withSum('sales', 'total')
            ->orderByDesc('opened_at')
            ->paginate(20)
            ->withQueryString();

        $summary = CashRegisterSession::query()
            ->when($from, fn ($query) => $query->whereDate('opened_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('opened_at', '<=', $to))
            ->selectRaw('COUNT(*) as session_count, COALESCE(SUM(opening_amount), 0) as total_opening, COALESCE(SUM(closing_amount), 0) as total_closing')
            ->first();

        return view('reports.cajas', compact('sessions', 'summary', 'from', 'to'));
    }
}
