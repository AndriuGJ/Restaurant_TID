@extends('layouts.app')

@section('title', 'Empresas clientes')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Empresas clientes</h1>
            <p class="mt-1 text-sm text-gray-600">Empresas que facturan al restaurante.</p>
        </div>
        @can('clientes-gestionar')
            <a href="{{ route('customers.companies.create') }}"
                class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Nueva empresa cliente
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
                @forelse ($companyClients as $companyClient)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $companyClient->ruc }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $companyClient->social_reason }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $companyClient->phone ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $companyClient->contact_person ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @include('_partials.status-badge', ['active' => $companyClient->status])
                        </td>
                        <td class="px-6 py-4 text-right">
                            @include('_partials.row-actions', [
                                'editUrl' => route('customers.companies.edit', $companyClient),
                                'deleteUrl' => route('customers.companies.destroy', $companyClient),
                                'deleteLabel' => 'esta empresa cliente',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                            No hay empresas clientes registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $companyClients->links() }}
    </div>
@endsection