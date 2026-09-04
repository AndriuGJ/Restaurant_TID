<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\StoreCustomerRequest;
use App\Http\Requests\Customers\UpdateCustomerRequest;
use App\Models\Configuration\DocumentType;
use App\Models\Customers\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::with('documentType')->latest('id')->paginate(15);

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        $documentTypes = $this->identificationDocumentTypes();

        return view('customers.create', compact('documentTypes'));
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        Customer::create($request->validated());

        Cache::forget('customers_list');

        return redirect()->route('customers.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Customer $customer): View
    {
        $documentTypes = $this->identificationDocumentTypes();

        return view('customers.edit', compact('customer', 'documentTypes'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        Cache::forget('customers_list');

        return redirect()->route('customers.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->sales()->exists()) {
            return redirect()
                ->route('customers.index')
                ->withErrors('No se puede eliminar un cliente que tiene ventas.');
        }

        $customer->delete();

        Cache::forget('customers_list');

        return redirect()->route('customers.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }

    private function identificationDocumentTypes(): Collection
    {
        return DocumentType::where('type', 'identification')
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
