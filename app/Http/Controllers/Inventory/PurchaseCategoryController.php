<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StorePurchaseCategoryRequest;
use App\Http\Requests\Inventory\UpdatePurchaseCategoryRequest;
use App\Models\Inventory\PurchaseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PurchaseCategoryController extends Controller
{
    public function index(): View
    {
        $purchaseCategories = PurchaseCategory::withCount('products')->latest('id')->paginate(15);

        return view('inventory.purchase-categories.index', compact('purchaseCategories'));
    }

    public function create(): View
    {
        return view('inventory.purchase-categories.create');
    }

    public function store(StorePurchaseCategoryRequest $request): RedirectResponse
    {
        PurchaseCategory::create($request->validated());

        Cache::forget('inventory_purchase_categories');

        return redirect()->route('inventory.purchase-categories.index')
            ->with('success', 'Categoría de compra creada correctamente.');
    }

    public function edit(PurchaseCategory $purchaseCategory): View
    {
        return view('inventory.purchase-categories.edit', compact('purchaseCategory'));
    }

    public function update(UpdatePurchaseCategoryRequest $request, PurchaseCategory $purchaseCategory): RedirectResponse
    {
        $purchaseCategory->update($request->validated());

        Cache::forget('inventory_purchase_categories');

        return redirect()->route('inventory.purchase-categories.index')
            ->with('success', 'Categoría de compra actualizada correctamente.');
    }

    public function destroy(PurchaseCategory $purchaseCategory): RedirectResponse
    {
        if ($purchaseCategory->products()->exists()) {
            return redirect()
                ->route('inventory.purchase-categories.index')
                ->withErrors('No se puede eliminar una categoría de compra que tiene productos.');
        }

        $purchaseCategory->delete();

        Cache::forget('inventory_purchase_categories');

        return redirect()->route('inventory.purchase-categories.index')
            ->with('success', 'Categoría de compra eliminada correctamente.');
    }
}
