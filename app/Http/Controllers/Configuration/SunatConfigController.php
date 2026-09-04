<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Configuration\StoreSunatConfigRequest;
use App\Http\Requests\Configuration\UpdateSunatConfigRequest;
use App\Models\Configuration\Company;
use App\Models\Configuration\SunatConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SunatConfigController extends Controller
{
    public function index(): View
    {
        $sunatConfigs = SunatConfig::with('company')->latest('id')->paginate(10);

        return view('configuration.sunat-configs.index', compact('sunatConfigs'));
    }

    public function create(): View
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);

        return view('configuration.sunat-configs.create', compact('companies'));
    }

    public function store(StoreSunatConfigRequest $request): RedirectResponse
    {
        SunatConfig::create($request->validated());

        Cache::forget('configuration_sunat_configs');

        return redirect()->route('configuration.sunat-configs.index')
            ->with('success', 'Configuración SUNAT creada correctamente.');
    }

    public function edit(SunatConfig $sunatConfig): View
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);

        return view('configuration.sunat-configs.edit', compact('sunatConfig', 'companies'));
    }

    public function update(UpdateSunatConfigRequest $request, SunatConfig $sunatConfig): RedirectResponse
    {
        $sunatConfig->update($request->validated());

        Cache::forget('configuration_sunat_configs');

        return redirect()->route('configuration.sunat-configs.index')
            ->with('success', 'Configuración SUNAT actualizada correctamente.');
    }

    public function destroy(SunatConfig $sunatConfig): RedirectResponse
    {
        $sunatConfig->delete();

        Cache::forget('configuration_sunat_configs');

        return redirect()->route('configuration.sunat-configs.index')
            ->with('success', 'Configuración SUNAT eliminada correctamente.');
    }
}
