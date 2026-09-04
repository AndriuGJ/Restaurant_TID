<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Configuration\StorePaymentMethodRequest;
use App\Http\Requests\Configuration\UpdatePaymentMethodRequest;
use App\Models\Configuration\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(): View
    {
        $paymentMethods = PaymentMethod::latest('id')->paginate(10);

        return view('configuration.payment-methods.index', compact('paymentMethods'));
    }

    public function create(): View
    {
        return view('configuration.payment-methods.create');
    }

    public function store(StorePaymentMethodRequest $request): RedirectResponse
    {
        PaymentMethod::create($request->validated());

        Cache::forget('configuration_payment_methods');

        return redirect()->route('configuration.payment-methods.index')
            ->with('success', 'Medio de pago creado correctamente.');
    }

    public function edit(PaymentMethod $paymentMethod): View
    {
        return view('configuration.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $paymentMethod->update($request->validated());

        Cache::forget('configuration_payment_methods');

        return redirect()->route('configuration.payment-methods.index')
            ->with('success', 'Medio de pago actualizado correctamente.');
    }

    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        if ($paymentMethod->salePayments()->exists()) {
            return redirect()
                ->route('configuration.payment-methods.index')
                ->withErrors('No se puede eliminar un medio de pago que ya fue utilizado.');
        }

        $paymentMethod->delete();

        Cache::forget('configuration_payment_methods');

        return redirect()->route('configuration.payment-methods.index')
            ->with('success', 'Medio de pago eliminado correctamente.');
    }
}
