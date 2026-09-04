<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\StoreDeliveryProviderRequest;
use App\Http\Requests\Restaurant\UpdateDeliveryProviderRequest;
use App\Models\Restaurant\DeliveryProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DeliveryProviderController extends Controller
{
    public function index(): View
    {
        $deliveryProviders = DeliveryProvider::latest('id')->paginate(10);

        return view('restaurant.delivery-providers.index', compact('deliveryProviders'));
    }

    public function create(): View
    {
        return view('restaurant.delivery-providers.create');
    }

    public function store(StoreDeliveryProviderRequest $request): RedirectResponse
    {
        DeliveryProvider::create($request->validated());

        Cache::forget('restaurant_delivery_providers');

        return redirect()->route('restaurant.delivery-providers.index')
            ->with('success', 'Proveedor de delivery creado correctamente.');
    }

    public function edit(DeliveryProvider $deliveryProvider): View
    {
        return view('restaurant.delivery-providers.edit', compact('deliveryProvider'));
    }

    public function update(UpdateDeliveryProviderRequest $request, DeliveryProvider $deliveryProvider): RedirectResponse
    {
        $deliveryProvider->update($request->validated());

        Cache::forget('restaurant_delivery_providers');

        return redirect()->route('restaurant.delivery-providers.index')
            ->with('success', 'Proveedor de delivery actualizado correctamente.');
    }

    public function destroy(DeliveryProvider $deliveryProvider): RedirectResponse
    {
        if ($deliveryProvider->sales()->exists()) {
            return redirect()
                ->route('restaurant.delivery-providers.index')
                ->withErrors('No se puede eliminar un proveedor de delivery que tiene ventas asociadas.');
        }

        $deliveryProvider->delete();

        Cache::forget('restaurant_delivery_providers');

        return redirect()->route('restaurant.delivery-providers.index')
            ->with('success', 'Proveedor de delivery eliminado correctamente.');
    }
}
