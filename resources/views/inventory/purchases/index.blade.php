@extends('layouts.app')

@section('title', 'Compras')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Compras</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Registro de compras a proveedores con entrada a inventario.</p>
        </div>
        @can('compras-gestionar')
            <a href="{{ route('inventory.purchases.create') }}"
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Nueva compra
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Comprobante</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Proveedor</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tipo</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($purchases as $purchase)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                            {{ $purchase->documentType?->nomenclature ?? $purchase->documentType?->name }} {{ $purchase->series }}-{{ $purchase->number }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $purchase->supplier?->social_reason }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($purchase->purchase_type) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600 dark:text-gray-400">S/ {{ number_format($purchase->total, 2) }}</td>
                        <td class="px-6 py-4">
                            @include('_partials.status-badge', ['active' => $purchase->status === 'completed'])
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('inventory.purchases.show', $purchase) }}"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            No hay compras registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $purchases->links() }}
    </div>
@endsection