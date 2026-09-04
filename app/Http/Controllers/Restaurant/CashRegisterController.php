<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\StoreCashRegisterRequest;
use App\Http\Requests\Restaurant\UpdateCashRegisterRequest;
use App\Models\Restaurant\CashRegister;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CashRegisterController extends Controller
{
    public function index(): View
    {
        $cashRegisters = CashRegister::withCount('cashRegisterSessions')->latest('id')->paginate(10);

        return view('restaurant.cash-registers.index', compact('cashRegisters'));
    }

    public function create(): View
    {
        return view('restaurant.cash-registers.create');
    }

    public function store(StoreCashRegisterRequest $request): RedirectResponse
    {
        CashRegister::create($request->validated());

        Cache::forget('restaurant_cash_registers');

        return redirect()->route('restaurant.cash-registers.index')
            ->with('success', 'Caja creada correctamente.');
    }

    public function edit(CashRegister $cashRegister): View
    {
        return view('restaurant.cash-registers.edit', compact('cashRegister'));
    }

    public function update(UpdateCashRegisterRequest $request, CashRegister $cashRegister): RedirectResponse
    {
        $cashRegister->update($request->validated());

        Cache::forget('restaurant_cash_registers');

        return redirect()->route('restaurant.cash-registers.index')
            ->with('success', 'Caja actualizada correctamente.');
    }

    public function destroy(CashRegister $cashRegister): RedirectResponse
    {
        if ($cashRegister->cashRegisterSessions()->exists()) {
            return redirect()
                ->route('restaurant.cash-registers.index')
                ->withErrors('No se puede eliminar una caja que tiene sesiones registradas.');
        }

        $cashRegister->delete();

        Cache::forget('restaurant_cash_registers');

        return redirect()->route('restaurant.cash-registers.index')
            ->with('success', 'Caja eliminada correctamente.');
    }
}
