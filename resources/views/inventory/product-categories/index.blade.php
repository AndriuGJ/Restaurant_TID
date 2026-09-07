@extends('layouts.app')

@section('title', 'Categorías de producto')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Categorías de producto</h1>
            <p class="mt-1 text-sm text-gray-600">Categorías de platos que se muestran en el POS.</p>
        </div>
        @can('inventario-ver')
            <a href="{{ route('inventory.product-categories.create') }}"
                class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Nueva categoría de producto
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Descripción</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Productos</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($productCategories as $productCategory)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $productCategory->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $productCategory->description ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $productCategory->products_count }}</td>
                        <td class="px-6 py-4">
                            @include('_partials.status-badge', ['active' => $productCategory->status])
                        </td>
                        <td class="px-6 py-4 text-right">
                            @include('_partials.row-actions', [
                                'editUrl' => route('inventory.product-categories.edit', $productCategory),
                                'deleteUrl' => route('inventory.product-categories.destroy', $productCategory),
                                'deleteLabel' => 'esta categoría de producto',
                                'permission' => 'inventario-ver',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                            No hay categorías de producto registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $productCategories->links() }}
    </div>
@endsection