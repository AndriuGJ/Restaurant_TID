<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreSupplierRequest;
use App\Http\Requests\Inventory\UpdateSupplierRequest;
use App\Models\Inventory\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::withCount('purchases')->latest('id')->paginate(15);

        return view('inventory.suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('inventory.suppliers.create');
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        Supplier::create($request->validated());

        Cache::forget('inventory_suppliers');

        return redirect()->route('inventory.suppliers.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    public function edit(Supplier $supplier): View
    {
        return view('inventory.suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        Cache::forget('inventory_suppliers');

        return redirect()->route('inventory.suppliers.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchases()->exists()) {
            return redirect()
                ->route('inventory.suppliers.index')
                ->withErrors('No se puede eliminar un proveedor que tiene compras.');
        }

        $supplier->delete();

        Cache::forget('inventory_suppliers');

        return redirect()->route('inventory.suppliers.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
