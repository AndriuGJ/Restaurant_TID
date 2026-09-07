<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Product;
use App\Models\Kardex\KardexMovement;
use App\Services\KardexExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class KardexController extends Controller
{
    public function __construct(
        private readonly KardexExportService $exportService,
    ) {}

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

    public function exportPdf(Request $request): RedirectResponse|Response
    {
        $productId = $request->integer('product');

        $movements = KardexMovement::with('product', 'relatedDocument')
            ->when($productId, fn ($query) => $query->where('product_id', $productId))
            ->latest('id')
            ->get();

        $productName = $productId
            ? Product::find($productId)?->name
            : null;

        return $this->exportService->exportPdf($movements, $productName);
    }

    public function exportExcel(Request $request): RedirectResponse|BinaryFileResponse
    {
        $productId = $request->integer('product');

        $movements = KardexMovement::with('product', 'relatedDocument')
            ->when($productId, fn ($query) => $query->where('product_id', $productId))
            ->latest('id')
            ->get();

        $productName = $productId
            ? Product::find($productId)?->name
            : null;

        return $this->exportService->exportExcel($movements, $productName);
    }
}
