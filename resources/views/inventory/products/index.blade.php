@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Productos</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Platos, insumos y combos del menú.</p>
        </div>
        @can('productos-gestionar')
            <a href="{{ route('inventory.products.create') }}"
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Nuevo producto
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Categoría</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Precio venta</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200">
                                {{ $product->type === 'dish' ? 'Plato' : ($product->type === 'combo' ? 'Combo' : 'Insumo') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $product->productCategory?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600 dark:text-gray-400">S/ {{ number_format($product->sale_price, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600 dark:text-gray-400">{{ number_format($product->stock, 2) }} {{ $product->unit_of_measure }}</td>
                        <td class="px-6 py-4">
                            @include('_partials.status-badge', ['active' => $product->status])
                        </td>
                        <td class="px-6 py-4 text-right">
                            @include('_partials.row-actions', [
                                'editUrl' => route('inventory.products.edit', $product),
                                'deleteUrl' => route('inventory.products.destroy', $product),
                                'deleteLabel' => 'este producto',
                                'permission' => 'productos-gestionar',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            No hay productos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
@endsection