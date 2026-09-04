@extends('layouts.app')

@section('title', 'Configuración SUNAT')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Configuración SUNAT</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Vigencia y tope de comprobantes por empresa.</p>
        </div>
        @can('configuracion-editar')
            <a href="{{ route('configuration.sunat-configs.create') }}"
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Nueva configuración
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Empresa</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Vigencia</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Comprobantes</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($sunatConfigs as $sunatConfig)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $sunatConfig->company->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $sunatConfig->start_date->format('d/m/Y') }} → {{ $sunatConfig->end_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                {{ $sunatConfig->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' : ($sunatConfig->status === 'expired' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200') }}">
                                {{ ucfirst($sunatConfig->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
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
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
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