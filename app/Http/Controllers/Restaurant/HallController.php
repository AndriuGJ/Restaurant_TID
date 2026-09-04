<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\StoreHallRequest;
use App\Http\Requests\Restaurant\UpdateHallRequest;
use App\Models\Restaurant\Hall;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HallController extends Controller
{
    public function index(): View
    {
        $halls = Hall::withCount('tables')->latest('id')->paginate(10);

        return view('restaurant.halls.index', compact('halls'));
    }

    public function create(): View
    {
        return view('restaurant.halls.create');
    }

    public function store(StoreHallRequest $request): RedirectResponse
    {
        Hall::create($request->validated());

        Cache::forget('restaurant_halls');

        return redirect()->route('restaurant.halls.index')
            ->with('success', 'Salón creado correctamente.');
    }

    public function edit(Hall $hall): View
    {
        return view('restaurant.halls.edit', compact('hall'));
    }

    public function update(UpdateHallRequest $request, Hall $hall): RedirectResponse
    {
        $hall->update($request->validated());

        Cache::forget('restaurant_halls');

        return redirect()->route('restaurant.halls.index')
            ->with('success', 'Salón actualizado correctamente.');
    }

    public function destroy(Hall $hall): RedirectResponse
    {
        if ($hall->tables()->exists()) {
            return redirect()
                ->route('restaurant.halls.index')
                ->withErrors('No se puede eliminar un salón que tiene mesas registradas.');
        }

        $hall->delete();

        Cache::forget('restaurant_halls');

        return redirect()->route('restaurant.halls.index')
            ->with('success', 'Salón eliminado correctamente.');
    }
}
