@extends('layouts.app')

@section('title', 'Mesas')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mesas</h1>
            <p class="mt-1 text-sm text-gray-600">Gestiona las mesas y su ubicación en los salones.</p>
        </div>
        @can('configuracion-editar')
            <a href="{{ route('restaurant.tables.create') }}"
                class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Nueva mesa
            </a>
        @endcan
    </div>

    <form method="GET" action="{{ route('restaurant.tables.index') }}" class="mt-4 flex max-w-sm items-end gap-2">
        <div class="flex-1">
            <label for="hall_id" class="block text-sm font-medium text-gray-700">Salón</label>
            <select id="hall_id" name="hall_id"
                class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Todos los salones</option>
                @foreach ($halls as $hall)
                    <option value="{{ $hall->id }}" @selected($hallId === $hall->id)>{{ $hall->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit"
            class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">Filtrar</button>
    </form>

    <div class="mt-6 overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Salón</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Forma</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($tables as $table)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $table->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $table->hall->name }}</td>
                        <td class="px-6 py-4 text-sm capitalize text-gray-600">{{ $table->shape }}</td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                {{ $table->status === 'available' ? 'bg-green-100 text-green-800' : ($table->status === 'occupied' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ match ($table->status) {
                                    'available' => 'Disponible',
                                    'occupied' => 'Ocupada',
                                    'reserved' => 'Reservada',
                                } }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @include('_partials.row-actions', [
                                'editUrl' => route('restaurant.tables.edit', $table),
                                'deleteUrl' => route('restaurant.tables.destroy', $table),
                                'deleteLabel' => 'esta mesa',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                            No hay mesas registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tables->links() }}
    </div>
@endsection
