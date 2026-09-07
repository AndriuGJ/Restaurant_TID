@extends('layouts.app')

@section('title', 'Proveedores')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Proveedores</h1>
            <p class="mt-1 text-sm text-gray-600">Empresas que abastecen el inventario del restaurante.</p>
        </div>
        @can('inventario-ver')
            <a href="{{ route('inventory.suppliers.create') }}"
                class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Nuevo proveedor
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">RUC</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Razón social</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Teléfono</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Contacto</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $supplier->ruc }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $supplier->social_reason }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $supplier->phone ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $supplier->contact_person ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @include('_partials.status-badge', ['active' => $supplier->status])
                        </td>
                        <td class="px-6 py-4 text-right">
                            @include('_partials.row-actions', [
                                'editUrl' => route('inventory.suppliers.edit', $supplier),
                                'deleteUrl' => route('inventory.suppliers.destroy', $supplier),
                                'deleteLabel' => 'este proveedor',
                                'permission' => 'inventario-ver',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                            No hay proveedores registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $suppliers->links() }}
    </div>
@endsection