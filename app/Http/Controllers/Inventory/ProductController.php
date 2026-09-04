<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreProductRequest;
use App\Http\Requests\Inventory\UpdateProductRequest;
use App\Models\Inventory\Product;
use App\Models\Inventory\ProductCategory;
use App\Models\Inventory\ProductIngredient;
use App\Models\Inventory\PurchaseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['productCategory', 'purchaseCategory'])
            ->latest('id')
            ->paginate(15);

        return view('inventory.products.index', compact('products'));
    }

    public function create(): View
    {
        $options = $this->formOptions();

        return view('inventory.products.create', $options);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = DB::transaction(function () use ($request) {
            $product = Product::create($request->validated());
            $this->syncIngredients($product, $request->input('ingredients', []));

            return $product;
        });

        Cache::forget('inventory_products');

        return redirect()->route('inventory.products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product): View
    {
        $product->load('ingredients.ingredient');
        $options = $this->formOptions();

        return view('inventory.products.edit', compact('product') + $options);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        DB::transaction(function () use ($request, $product) {
            $product->update($request->validated());
            $this->syncIngredients($product, $request->input('ingredients', []));
        });

        Cache::forget('inventory_products');

        return redirect()->route('inventory.products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->purchaseDetails()->exists() || $product->saleDetails()->exists()) {
            return redirect()
                ->route('inventory.products.index')
                ->withErrors('No se puede eliminar un producto que tiene compras o ventas.');
        }

        DB::transaction(function () use ($product) {
            $product->ingredients()->delete();
            $product->delete();
        });

        Cache::forget('inventory_products');

        return redirect()->route('inventory.products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * @param  array<array{ingredient_id?: int, quantity?: mixed}>  $ingredients
     */
    private function syncIngredients(Product $product, array $ingredients): void
    {
        $rows = collect($ingredients)
            ->filter(fn (array $row) => isset($row['ingredient_id']))
            ->map(fn (array $row) => [
                'dish_id' => $product->id,
                'ingredient_id' => $row['ingredient_id'],
                'quantity' => $row['quantity'],
            ]);

        ProductIngredient::where('dish_id', $product->id)->delete();
        ProductIngredient::insert($rows->all());
    }

    /**
     * @return array{productCategories: Collection, purchaseCategories: Collection, supplies: Collection}
     */
    private function formOptions(): array
    {
        return [
            'productCategories' => ProductCategory::orderBy('name')->get(['id', 'name']),
            'purchaseCategories' => PurchaseCategory::orderBy('name')->get(['id', 'name']),
            'supplies' => Product::where('type', 'supply')->where('status', true)->orderBy('name')->get(['id', 'name']),
        ];
    }
}
