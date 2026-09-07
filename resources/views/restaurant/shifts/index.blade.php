@extends('layouts.app')

@section('title', 'Turnos')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Turnos</h1>
            <p class="mt-1 text-sm text-gray-600">Gestiona los turnos de trabajo del restaurante.</p>
        </div>
        @can('configuracion-editar')
            <a href="{{ route('restaurant.shifts.create') }}"
                class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Nuevo turno
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($shifts as $shift)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $shift->name }}</td>
                        <td class="px-6 py-4">
                            @include('_partials.status-badge', ['active' => $shift->status])
                        </td>
                        <td class="px-6 py-4 text-right">
                            @include('_partials.row-actions', [
                                'editUrl' => route('restaurant.shifts.edit', $shift),
                                'deleteUrl' => route('restaurant.shifts.destroy', $shift),
                                'deleteLabel' => 'este turno',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500">
                            No hay turnos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $shifts->links() }}
    </div>
@endsection
