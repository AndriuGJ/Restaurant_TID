<?php

namespace App\Http\Controllers;

use App\Models\Inventory\Product;
use App\Models\Restaurant\CashRegisterSession;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();

        $stats = [
            'salesToday' => Sale::query()->whereDate('created_at', $today)->count(),
            'salesTodayTotal' => (float) Sale::query()->where('status', 'paid')
                ->whereDate('created_at', $today)
                ->sum('total'),
            'pendingSales' => Sale::query()->where('status', 'pending')->count(),
            'kitchenPending' => SaleDetail::query()->whereIn('kitchen_status', ['pending', 'preparing'])->count(),
            'openCashSession' => CashRegisterSession::query()->where('status', 'open')->first(),
            'lowStockProducts' => Product::query()
                ->where('status', true)
                ->where('stock', '<=', 10)
                ->orderBy('stock')
                ->limit(8)
                ->get(),
            'lowStockCount' => Product::query()
                ->where('status', true)
                ->where('stock', '<=', 10)
                ->count(),
            'usersCount' => User::query()->count(),
        ];

        $shortcuts = [
            ['label' => 'Salón', 'route' => 'pos.hall', 'icon' => 'hall', 'desc' => 'Vender en mesas', 'permission' => 'pos-ver'],
            ['label' => 'Venta rápida', 'route' => 'pos.new.quick-sale', 'icon' => 'rapid', 'desc' => 'Cobrar al vuelo', 'permission' => 'pos-venta-rapida'],
            ['label' => 'Delivery', 'route' => 'pos.new.delivery', 'icon' => 'delivery', 'desc' => 'Pedidos a domicilio', 'permission' => 'pos-delivery'],
            ['label' => 'Cocina', 'route' => 'pos.kitchen.index', 'icon' => 'kitchen', 'desc' => 'Órdenes en preparación', 'permission' => 'pos-preparacion'],
            ['label' => 'Reporte de ventas', 'route' => 'reportes.ventas', 'icon' => 'report', 'desc' => 'Ver ventas por rango', 'permission' => 'reportes-ver'],
            ['label' => 'Abrir / Cerrar caja', 'route' => 'cash-registers.sessions.index', 'icon' => 'cash', 'desc' => 'Gestionar sesiones de caja', 'permission' => 'cajas-ver'],
            ['label' => 'Productos', 'route' => 'inventory.products.index', 'icon' => 'products', 'desc' => 'Inventario y stock', 'permission' => 'inventario-ver'],
            ['label' => 'Clientes', 'route' => 'customers.index', 'icon' => 'clients', 'desc' => 'Catálogo de clientes', 'permission' => 'clientes-ver'],
        ];

        $shortcuts = collect($shortcuts)->filter(function ($shortcut) {
            return auth()->user()->can($shortcut['permission']);
        })->values();

        return view('dashboard.index', compact('stats', 'shortcuts'));
    }
}
