@extends('layouts.app')

@section('title', 'Kardex')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kardex</h1>
            <p class="mt-1 text-sm text-gray-600">Movimientos de inventario (solo lectura).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('inventory.kardex.export-pdf', request()->query()) }}" target="_blank"
                class="inline-flex items-center gap-1.5 rounded-md border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:border-brand-300 hover:text-brand-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                PDF
            </a>
            <a href="{{ route('inventory.kardex.export-excel', request()->query()) }}"
                class="inline-flex items-center gap-1.5 rounded-md border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:border-brand-300 hover:text-brand-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Excel
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('inventory.kardex.index') }}" class="mt-4 flex max-w-sm items-end gap-2">
        <div class="flex-1">
            <label for="product" class="block text-sm font-medium text-gray-700">Producto</label>
            <select id="product" name="product"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Todos los productos</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected($productId === $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit"
            class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
            Filtrar
        </button>
    </form>

    <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Movimiento</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Entrada</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Salida</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Saldo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Documento</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($movements as $movement)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $movement->product?->name }}</td>
                        <td class="px-6 py-4">
                            @php
                                $typeClasses = [
                                    'purchase' => 'bg-green-100 text-green-800',
                                    'sale' => 'bg-red-100 text-red-800',
                                    'adjustment' => 'bg-amber-100 text-amber-800',
                                ];
                                $typeLabels = [
                                    'purchase' => 'Compra',
                                    'sale' => 'Venta',
                                    'adjustment' => 'Ajuste',
                                ];
                            @endphp
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $typeClasses[$movement->movement_type] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $typeLabels[$movement->movement_type] ?? ucfirst($movement->movement_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-green-600">{{ $movement->quantity_in > 0 ? number_format($movement->quantity_in, 2) : '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm text-red-600">{{ $movement->quantity_out > 0 ? number_format($movement->quantity_out, 2) : '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ number_format($movement->balance, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $movement->relatedDocument ? class_basename($movement->related_document_type).' #'.$movement->related_document_id : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                            No hay movimientos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $movements->links() }}
    </div>
@endsection
