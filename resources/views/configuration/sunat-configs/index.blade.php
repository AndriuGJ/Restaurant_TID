@extends('layouts.app')

@section('title', 'Configuración SUNAT')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Configuración SUNAT</h1>
            <p class="mt-1 text-sm text-gray-600">Vigencia y tope de comprobantes por empresa.</p>
        </div>
        @can('configuracion-editar')
            <a href="{{ route('configuration.sunat-configs.create') }}"
                class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Nueva configuración
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Empresa</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Comprobante</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Vigencia</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Comprobantes</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($sunatConfigs as $sunatConfig)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $sunatConfig->company->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $sunatConfig->documentType?->name ?? '—' }}
                            ({{ $sunatConfig->documentType?->nomenclature }}001)
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $sunatConfig->start_date->format('d/m/Y') }} → {{ $sunatConfig->end_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                {{ $sunatConfig->status === 'active' ? 'bg-green-100 text-green-800' : ($sunatConfig->status === 'expired' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($sunatConfig->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $sunatConfig->used_receipts }} / {{ $sunatConfig->max_receipts }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            @include('_partials.row-actions', [
                                'editUrl' => route('configuration.sunat-configs.edit', $sunatConfig),
                                'deleteUrl' => route('configuration.sunat-configs.destroy', $sunatConfig),
                                'deleteLabel' => 'esta configuración',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                            No hay configuraciones SUNAT registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sunatConfigs->links() }}
    </div>
@endsection