@extends('layouts.app')

@section('title', 'Compras')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Compras</h1>
            <p class="mt-1 text-sm text-gray-600">Registro de compras a proveedores con entrada a inventario.</p>
        </div>
        @can('compras-gestionar')
            <a href="{{ route('inventory.purchases.create') }}"
                class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Nueva compra
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Comprobante</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Proveedor</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tipo</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($purchases as $purchase)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $purchase->documentType?->nomenclature ?? $purchase->documentType?->name }} {{ $purchase->series }}-{{ $purchase->number }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->supplier?->social_reason }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($purchase->purchase_type) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600">S/ {{ number_format($purchase->total, 2) }}</td>
                        <td class="px-6 py-4">
                            @include('_partials.status-badge', ['active' => $purchase->status === 'completed'])
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('inventory.purchases.show', $purchase) }}"
                                class="text-sm font-medium text-brand-600 hover:text-brand-500">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
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
