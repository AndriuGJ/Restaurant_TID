@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Productos</h1>
            <p class="mt-1 text-sm text-gray-600">Platos, insumos y combos del menú.</p>
        </div>
        @can('productos-gestionar')
            <a href="{{ route('inventory.products.create') }}"
                class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Nuevo producto
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Imagen</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Categoría</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Precio venta</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-6 py-4">
                            @if ($product->image_url)
                                <img src="{{ asset('storage/'.$product->image_url) }}" alt="{{ $product->name }}"
                                    class="h-10 w-10 rounded-md object-cover">
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded-md bg-gray-100 text-gray-400">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h15a1.5 1.5 0 011.5 1.5v7.5a1.5 1.5 0 01-1.5 1.5H6.75a1.5 1.5 0 01-1.5 1.5v4.5"/>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4">
                            @php
                                $typeClasses = [
                                    'dish' => 'bg-brand-100 text-brand-800',
                                    'combo' => 'bg-purple-100 text-purple-800',
                                    'supply' => 'bg-gray-100 text-gray-800',
                                ];
                                $typeLabels = ['dish' => 'Plato', 'combo' => 'Combo', 'supply' => 'Insumo'];
                            @endphp
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $typeClasses[$product->type] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $typeLabels[$product->type] ?? $product->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->productCategory?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600">S/ {{ number_format($product->sale_price, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600">{{ number_format($product->stock, 2) }} {{ $product->unit_of_measure }}</td>
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
                        <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">
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
