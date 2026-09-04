@extends('layouts.app')

@section('title', 'Reporte de Cajas')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reporte de Cajas</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Historial de aperturas y cierres de caja, montos y responsables.</p>
        </div>
        <button type="button" onclick="window.print()"
            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            Imprimir reporte
        </button>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('reportes.cajas') }}" class="mt-6 flex items-end gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Desde</label>
            <input type="date" name="from" value="{{ $from }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hasta</label>
            <input type="date" name="to" value="{{ $to }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>
        <button type="submit"
            class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 dark:bg-gray-200 dark:text-gray-900 dark:hover:bg-gray-300">
            Filtrar
        </button>
    </form>

    {{-- Resumen --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Sesiones</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary->session_count, 0) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total apertura</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">S/ {{ number_format($summary->total_opening, 2) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total cierre</p>
            <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">S/ {{ number_format($summary->total_closing, 2) }}</p>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Caja</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Turno</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Abierta por</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Cerrada por</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Apertura</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Ventas</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Cierre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Fechas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($sessions as $session)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $session->cashRegister->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $session->shift->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $session->userOpening->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $session->userClosing?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600 dark:text-gray-400">S/ {{ number_format($session->opening_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ $session->sales_count }}
                            <span class="text-gray-500 dark:text-gray-400">(S/ {{ number_format((float) $session->sales_sum_total ?? 0, 2) }})</span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600 dark:text-gray-400">
                            {{ $session->closing_amount !== null ? 'S/ '.number_format($session->closing_amount, 2) : '—' }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $open = $session->status === 'open';
                            @endphp
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $open ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200' }}">
                                {{ $open ? 'Abierta' : 'Cerrada' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $session->opened_at?->format('d/m/Y H:i') }}
                            @if ($session->closed_at)
                                <br>{{ $session->closed_at->format('d/m/Y H:i') }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No hay sesiones de caja para los filtros indicados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sessions->links() }}
    </div>
@endsection