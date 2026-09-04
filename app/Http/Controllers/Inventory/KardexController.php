<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Product;
use App\Models\Kardex\KardexMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KardexController extends Controller
{
    public function index(Request $request): View
    {
        $productId = $request->integer('product');

        $movements = KardexMovement::with('product', 'relatedDocument')
            ->when($productId, fn ($query) => $query->where('product_id', $productId))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $products = Product::orderBy('name')->get(['id', 'name']);

        return view('inventory.kardex.index', compact('movements', 'products', 'productId'));
    }
}
