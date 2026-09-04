<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\StoreTableRequest;
use App\Http\Requests\Restaurant\UpdateTableRequest;
use App\Models\Restaurant\Hall;
use App\Models\Restaurant\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TableController extends Controller
{
    public function index(): View
    {
        $tables = Table::with('hall')->latest('id')->paginate(10);

        return view('restaurant.tables.index', compact('tables'));
    }

    public function create(): View
    {
        $halls = Hall::where('status', true)->orderBy('name')->get();

        return view('restaurant.tables.create', compact('halls'));
    }

    public function store(StoreTableRequest $request): RedirectResponse
    {
        Table::create($request->validated());

        Cache::forget('restaurant_tables');

        return redirect()->route('restaurant.tables.index')
            ->with('success', 'Mesa creada correctamente.');
    }

    public function edit(Table $table): View
    {
        $halls = Hall::where('status', true)->orderBy('name')->get()->push($table->hall)->unique('id');

        return view('restaurant.tables.edit', compact('table', 'halls'));
    }

    public function update(UpdateTableRequest $request, Table $table): RedirectResponse
    {
        $table->update($request->validated());

        Cache::forget('restaurant_tables');

        return redirect()->route('restaurant.tables.index')
            ->with('success', 'Mesa actualizada correctamente.');
    }

    public function destroy(Table $table): RedirectResponse
    {
        if ($table->sales()->exists() || $table->saleTables()->exists()) {
            return redirect()
                ->route('restaurant.tables.index')
                ->withErrors('No se puede eliminar una mesa que tiene ventas asociadas.');
        }

        $table->delete();

        Cache::forget('restaurant_tables');

        return redirect()->route('restaurant.tables.index')
            ->with('success', 'Mesa eliminada correctamente.');
    }
}
