@extends('layouts.app')

@section('title', 'Medios de pago')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Medios de pago</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Configura las formas de pago aceptadas en el restaurante.</p>
        </div>
        @can('configuracion-editar')
            <a href="{{ route('configuration.payment-methods.create') }}"
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Nuevo medio de pago
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($paymentMethods as $paymentMethod)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $paymentMethod->name }}</td>
                        <td class="px-6 py-4 text-sm capitalize text-gray-600 dark:text-gray-400">
                            {{ match ($paymentMethod->type) {
                                'cash' => 'Efectivo',
                                'card' => 'Tarjeta',
                                'digital' => 'Digital',
                            } }}
                        </td>
                        <td class="px-6 py-4">
                            @include('_partials.status-badge', ['active' => $paymentMethod->status])
                        </td>
                        <td class="px-6 py-4 text-right">
                            @include('_partials.row-actions', [
                                'editUrl' => route('configuration.payment-methods.edit', $paymentMethod),
                                'deleteUrl' => route('configuration.payment-methods.destroy', $paymentMethod),
                                'deleteLabel' => 'este medio de pago',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            No hay medios de pago registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $paymentMethods->links() }}
    </div>
@endsection
