@extends('layouts.app')

@section('title', 'Salones')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Salones</h1>
            <p class="mt-1 text-sm text-gray-600">Gestiona los salones del restaurante.</p>
        </div>
        @can('configuracion-editar')
            <a href="{{ route('restaurant.halls.create') }}"
                class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Nuevo salón
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Mesas</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($halls as $hall)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $hall->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $hall->tables_count }}</td>
                        <td class="px-6 py-4">
                            @include('_partials.status-badge', ['active' => $hall->status])
                        </td>
                        <td class="px-6 py-4 text-right">
                            @include('_partials.row-actions', [
                                'editUrl' => route('restaurant.halls.edit', $hall),
                                'deleteUrl' => route('restaurant.halls.destroy', $hall),
                                'deleteLabel' => 'este salón',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                            No hay salones registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $halls->links() }}
    </div>
@endsection
