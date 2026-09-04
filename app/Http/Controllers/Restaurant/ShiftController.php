<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\StoreShiftRequest;
use App\Http\Requests\Restaurant\UpdateShiftRequest;
use App\Models\Restaurant\Shift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function index(): View
    {
        $shifts = Shift::withCount('cashRegisterSessions')->latest('id')->paginate(10);

        return view('restaurant.shifts.index', compact('shifts'));
    }

    public function create(): View
    {
        return view('restaurant.shifts.create');
    }

    public function store(StoreShiftRequest $request): RedirectResponse
    {
        Shift::create($request->validated());

        Cache::forget('restaurant_shifts');

        return redirect()->route('restaurant.shifts.index')
            ->with('success', 'Turno creado correctamente.');
    }

    public function edit(Shift $shift): View
    {
        return view('restaurant.shifts.edit', compact('shift'));
    }

    public function update(UpdateShiftRequest $request, Shift $shift): RedirectResponse
    {
        $shift->update($request->validated());

        Cache::forget('restaurant_shifts');

        return redirect()->route('restaurant.shifts.index')
            ->with('success', 'Turno actualizado correctamente.');
    }

    public function destroy(Shift $shift): RedirectResponse
    {
        if ($shift->cashRegisterSessions()->exists()) {
            return redirect()
                ->route('restaurant.shifts.index')
                ->withErrors('No se puede eliminar un turno que tiene sesiones de caja registradas.');
        }

        $shift->delete();

        Cache::forget('restaurant_shifts');

        return redirect()->route('restaurant.shifts.index')
            ->with('success', 'Turno eliminado correctamente.');
    }
}
