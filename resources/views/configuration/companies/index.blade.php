@extends('layouts.app')

@section('title', 'Empresa')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Empresa</h1>
            <p class="mt-1 text-sm text-gray-600">Datos de la empresa del sistema y su configuración fiscal.</p>
        </div>
        @if ($company)
            @can('configuracion-editar')
                <a href="{{ route('configuration.companies.edit', $company) }}"
                    class="inline-flex items-center gap-2 rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0121 21H3a2.25 2.25 0 01-2.25-2.25V3A2.25 2.25 0 015.25.75h7.5" />
                    </svg>
                    Editar empresa
                </a>
            @endcan
        @endif
    </div>

    @if ($company)
        <div class="mt-6 overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
            {{-- Encabezado con icono --}}
            <div class="flex flex-col gap-4 border-b border-gray-200 bg-gray-50 px-6 py-5 sm:flex-row sm:items-center">
                @if ($company->logo)
                    <img src="{{ asset('storage/'.$company->logo) }}" alt="{{ $company->name }}"
                        class="h-20 w-20 shrink-0 rounded-lg border border-gray-200 bg-white object-contain p-1">
                @else
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white">
                        <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-18 3h12m-12 9h6" />
                        </svg>
                    </div>
                @endif
                <div class="min-w-0">
                    <h2 class="text-lg font-bold text-gray-900">{{ $company->name }}</h2>
                    @if ($company->commercial_name)
                        <p class="text-sm text-gray-600">{{ $company->commercial_name }}</p>
                    @endif
                    <p class="mt-1 flex items-center gap-1 text-sm text-gray-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                        </svg>
                        RUC {{ $company->ruc }} · {{ $company->social_reason }}
                    </p>
                </div>
            </div>

            {{-- Detalles --}}
            <dl class="grid grid-cols-1 gap-x-8 gap-y-5 px-6 py-6 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Teléfono</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $company->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Dirección comercial</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $company->commercial_address ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Dirección fiscal</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $company->fiscal_address ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Ubicación (Ubigeo)</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if ($company->ubigeo)
                            {{ $company->ubigeo->department }} / {{ $company->ubigeo->province }} / {{ $company->ubigeo->district }}
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Usuario SOL</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $company->sol_user ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Icono para comprobantes</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $company->logo ? 'Cargado' : 'Sin icono' }}</dd>
                </div>
            </dl>
        </div>
    @else
        <div class="mt-6 rounded-md border border-dashed border-gray-300 bg-white p-10 text-center">
            <p class="text-sm text-gray-500">Aún no se han registrado los datos de la empresa.</p>
        </div>
    @endif
@endsection