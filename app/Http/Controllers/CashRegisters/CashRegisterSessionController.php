<?php

namespace App\Http\Controllers\CashRegisters;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashRegisters\CloseCashRegisterSessionRequest;
use App\Http\Requests\CashRegisters\OpenCashRegisterSessionRequest;
use App\Models\Restaurant\CashRegister;
use App\Models\Restaurant\CashRegisterSession;
use App\Models\Restaurant\Shift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CashRegisterSessionController extends Controller
{
    public function index(): View
    {
        $sessions = CashRegisterSession::with(['cashRegister', 'shift', 'userOpening', 'userClosing'])
            ->latest('id')
            ->paginate(15);

        return view('cash-registers.index', compact('sessions'));
    }

    public function create(): View
    {
        $cashRegisters = CashRegister::withCount('cashRegisterSessions')->orderBy('name')->get(['id', 'name']);
        $shifts = Shift::orderBy('name')->get(['id', 'name']);
        $suggestedOpenings = [];

        foreach ($cashRegisters as $cashRegister) {
            $suggested = self::suggestedOpeningAmount($cashRegister);
            if ($suggested !== null) {
                $suggestedOpenings[$cashRegister->id] = $suggested;
            }
        }

        $selectedOpeningAmount = old('opening_amount', $suggestedOpenings ? array_values($suggestedOpenings)[0] : null);

        return view('cash-registers.create', compact('cashRegisters', 'shifts', 'suggestedOpenings', 'selectedOpeningAmount'));
    }

    public function store(OpenCashRegisterSessionRequest $request): RedirectResponse
    {
        CashRegisterSession::create([
            'cash_register_id' => $request->cash_register_id,
            'shift_id' => $request->shift_id,
            'user_opening_id' => $request->user()->id,
            'opening_amount' => $request->opening_amount,
            'status' => 'open',
            'opened_at' => Carbon::now(),
        ]);

        Cache::forget('restaurant_cash_register_sessions');

        return redirect()->route('cash-registers.sessions.index')
            ->with('success', 'Caja abierta correctamente.');
    }

    public function edit(CashRegisterSession $session): View
    {
        return view('cash-registers.edit', compact('session'));
    }

    public function update(CloseCashRegisterSessionRequest $request, CashRegisterSession $session): RedirectResponse
    {
        $session->update([
            'user_closing_id' => $request->user()->id,
            'closing_amount' => $request->closing_amount,
            'status' => 'closed',
            'closed_at' => Carbon::now(),
        ]);

        Cache::forget('restaurant_cash_register_sessions');

        return redirect()->route('cash-registers.sessions.index')
            ->with('success', 'Caja cerrada correctamente.');
    }

    public static function suggestedOpeningAmount(CashRegister $cashRegister): ?string
    {
        $lastSession = CashRegisterSession::where('cash_register_id', $cashRegister->id)
            ->where('status', 'closed')
            ->latest('id')
            ->first();

        return $lastSession?->closing_amount;
    }
}
