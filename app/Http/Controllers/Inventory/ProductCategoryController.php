<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreProductCategoryRequest;
use App\Http\Requests\Inventory\UpdateProductCategoryRequest;
use App\Models\Inventory\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ProductCategoryController extends Controller
{
    public function index(): View
    {
        $productCategories = ProductCategory::withCount('products')->latest('id')->paginate(15);

        return view('inventory.product-categories.index', compact('productCategories'));
    }

    public function create(): View
    {
        return view('inventory.product-categories.create');
    }

    public function store(StoreProductCategoryRequest $request): RedirectResponse
    {
        ProductCategory::create($request->validated());

        Cache::forget('inventory_product_categories');

        return redirect()->route('inventory.product-categories.index')
            ->with('success', 'Categoría de producto creada correctamente.');
    }

    public function edit(ProductCategory $productCategory): View
    {
        return view('inventory.product-categories.edit', compact('productCategory'));
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory): RedirectResponse
    {
        $productCategory->update($request->validated());

        Cache::forget('inventory_product_categories');

        return redirect()->route('inventory.product-categories.index')
            ->with('success', 'Categoría de producto actualizada correctamente.');
    }

    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        if ($productCategory->products()->exists()) {
            return redirect()
                ->route('inventory.product-categories.index')
                ->withErrors('No se puede eliminar una categoría de producto que tiene productos.');
        }

        $productCategory->delete();

        Cache::forget('inventory_product_categories');

        return redirect()->route('inventory.product-categories.index')
            ->with('success', 'Categoría de producto eliminada correctamente.');
    }
}
