<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Configuration\UpdateCompanyRequest;
use App\Models\Configuration\Company;
use App\Models\Configuration\Ubigeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        $company = Company::with('ubigeo')->first();

        return view('configuration.companies.index', compact('company'));
    }

    public function edit(Company $company): View
    {
        $ubigeos = $this->ubigeosForSelect();

        return view('configuration.companies.edit', compact('company', 'ubigeos'));
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['sol_password'])) {
            unset($data['sol_password']);
        }

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }

            $data['logo'] = $request->file('logo')->store('logos', 'public');
        } elseif (! array_key_exists('logo', $data)) {
            unset($data['logo']);
        }

        $company->update($data);

        Cache::forget('configuration_companies');

        return redirect()->route('configuration.companies.index')
            ->with('success', 'Empresa actualizada correctamente.');
    }

    private function ubigeosForSelect(): Collection
    {
        return Ubigeo::orderBy('department')
            ->orderBy('province')
            ->orderBy('district')
            ->get(['id', 'department', 'province', 'district']);
    }
}