<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function index(): View
    {
        $orders = SaleDetail::with(['sale.table', 'sale.deliveryProvider', 'product'])
            ->where('kitchen_status', 'pending')
            ->orderBy('id')
            ->get()
            ->groupBy('sale_id');

        $totalPending = SaleDetail::where('kitchen_status', 'pending')->count();

        return view('pos.kitchen', compact('orders', 'totalPending'));
    }

    public function start(SaleDetail $detail): RedirectResponse
    {
        $detail->update([
            'kitchen_status' => 'preparing',
            'prep_started_at' => now(),
        ]);

        return redirect()->route('pos.kitchen.index');
    }

    public function complete(SaleDetail $detail): RedirectResponse
    {
        $detail->update([
            'kitchen_status' => 'completed',
            'prep_completed_at' => now(),
        ]);

        $this->updateSaleStatusIfDone($detail->sale_id);

        return redirect()->route('pos.kitchen.index');
    }

    private function updateSaleStatusIfDone(int $saleId): void
    {
        $remaining = SaleDetail::where('sale_id', $saleId)
            ->whereIn('kitchen_status', ['pending', 'preparing'])
            ->count();

        if ($remaining === 0) {
            $sale = Sale::find($saleId);
            if ($sale && $sale->status === 'preparing') {
                $sale->update(['status' => 'pending']);
            }
        }
    }
}
