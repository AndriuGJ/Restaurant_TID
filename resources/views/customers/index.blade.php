@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Clientes</h1>
            <p class="mt-1 text-sm text-gray-600">Personas naturales que compran en el restaurante.</p>
        </div>
        @can('clientes-gestionar')
            <a href="{{ route('customers.create') }}"
                class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Nuevo cliente
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Documento</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Teléfono</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Correo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($customers as $customer)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $customer->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if ($customer->documentType)
                                <span class="font-medium">{{ $customer->documentType->name }}</span>: {{ $customer->document_number }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->phone ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->email ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @include('_partials.status-badge', ['active' => $customer->status])
                        </td>
                        <td class="px-6 py-4 text-right">
                            @include('_partials.row-actions', [
                                'editUrl' => route('customers.edit', $customer),
                                'deleteUrl' => route('customers.destroy', $customer),
                                'deleteLabel' => 'este cliente',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                            No hay clientes registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $customers->links() }}
    </div>
@endsection