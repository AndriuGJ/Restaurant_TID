@extends('layouts.app')

@section('title', 'Kardex')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kardex</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Movimientos de inventario (solo lectura).</p>
        </div>
    </div>

    <form method="GET" action="{{ route('inventory.kardex.index') }}" class="mt-4 flex max-w-sm items-end gap-2">
        <div class="flex-1">
            <label for="product" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Producto</label>
            <select id="product" name="product"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <option value="">Todos los productos</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected($productId === $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit"
            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            Filtrar
        </button>
    </form>

    <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Movimiento</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Entrada</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Salida</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Saldo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Documento</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($movements as $movement)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $movement->product?->name }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                {{ $movement->movement_type === 'purchase' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200'
                                   : ($movement->movement_type === 'sale' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200'
                                   : 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200') }}">
                                @php
                                    $typeLabel = [
                                        'purchase' => 'Compra',
                                        'sale' => 'Venta',
                                        'adjustment' => 'Ajuste',
                                    ];
                                @endphp
                                {{ $typeLabel[$movement->movement_type] ?? ucfirst($movement->movement_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-green-600 dark:text-green-400">{{ $movement->quantity_in > 0 ? number_format($movement->quantity_in, 2) : '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm text-red-600 dark:text-red-400">{{ $movement->quantity_out > 0 ? number_format($movement->quantity_out, 2) : '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">{{ number_format($movement->balance, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $movement->relatedDocument ? class_basename($movement->related_document_type).' #'.$movement->related_document_id : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
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