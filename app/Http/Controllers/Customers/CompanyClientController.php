<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\StoreCompanyClientRequest;
use App\Http\Requests\Customers\UpdateCompanyClientRequest;
use App\Models\Configuration\DocumentType;
use App\Models\Customers\CompanyClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CompanyClientController extends Controller
{
    public function index(): View
    {
        $companyClients = CompanyClient::with('documentType')->latest('id')->paginate(15);

        return view('company-clients.index', compact('companyClients'));
    }

    public function create(): View
    {
        $documentTypes = $this->identificationDocumentTypes();

        return view('company-clients.create', compact('documentTypes'));
    }

    public function store(StoreCompanyClientRequest $request): RedirectResponse
    {
        CompanyClient::create($request->validated());

        Cache::forget('company_clients_list');

        return redirect()->route('customers.companies.index')
            ->with('success', 'Empresa cliente creada correctamente.');
    }

    public function edit(CompanyClient $companyClient): View
    {
        $documentTypes = $this->identificationDocumentTypes();

        return view('company-clients.edit', compact('companyClient', 'documentTypes'));
    }

    public function update(UpdateCompanyClientRequest $request, CompanyClient $companyClient): RedirectResponse
    {
        $companyClient->update($request->validated());

        Cache::forget('company_clients_list');

        return redirect()->route('customers.companies.index')
            ->with('success', 'Empresa cliente actualizada correctamente.');
    }

    public function destroy(CompanyClient $companyClient): RedirectResponse
    {
        if ($companyClient->sales()->exists()) {
            return redirect()
                ->route('customers.companies.index')
                ->withErrors('No se puede eliminar una empresa cliente que tiene ventas.');
        }

        $companyClient->delete();

        Cache::forget('company_clients_list');

        return redirect()->route('customers.companies.index')
            ->with('success', 'Empresa cliente eliminada correctamente.');
    }

    private function identificationDocumentTypes(): Collection
    {
        return DocumentType::where('type', 'identification')
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
