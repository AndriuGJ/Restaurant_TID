@extends('layouts.app')

@section('title', 'Cajas')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Cajas</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Gestiona las cajas del restaurante.</p>
        </div>
        @can('configuracion-editar')
            <a href="{{ route('restaurant.cash-registers.create') }}"
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Nueva caja
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($cashRegisters as $cashRegister)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $cashRegister->name }}</td>
                        <td class="px-6 py-4">
                            @include('_partials.status-badge', ['active' => $cashRegister->status])
                        </td>
                        <td class="px-6 py-4 text-right">
                            @include('_partials.row-actions', [
                                'editUrl' => route('restaurant.cash-registers.edit', $cashRegister),
                                'deleteUrl' => route('restaurant.cash-registers.destroy', $cashRegister),
                                'deleteLabel' => 'esta caja',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            No hay cajas registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $cashRegisters->links() }}
    </div>
@endsection
