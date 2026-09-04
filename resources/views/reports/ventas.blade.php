@extends('layouts.app')

@section('title', 'Reporte de Ventas')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reporte de Ventas</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Resumen y detalle de ventas (salón, delivery y venta rápida) en un rango de fechas.</p>
        </div>
        <button type="button" onclick="window.print()"
            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            Imprimir reporte
        </button>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('reportes.ventas') }}" class="mt-6 grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-4 dark:border-gray-800 dark:bg-gray-900">
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
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de venta</label>
            <select name="sale_type"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <option value="">Todos</option>
                <option value="pos" @selected($saleType === 'pos')>Salón</option>
                <option value="delivery" @selected($saleType === 'delivery')>Delivery</option>
                <option value="quick_sale" @selected($saleType === 'quick_sale')>Venta rápida</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Comprobante</label>
            <select name="document_type_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <option value="">Todos</option>
                @foreach ($documentTypes as $documentType)
                    <option value="{{ $documentType->id }}" @selected($documentTypeId == $documentType->id)>{{ $documentType->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-4">
            <button type="submit"
                class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 dark:bg-gray-200 dark:text-gray-900 dark:hover:bg-gray-300">
                Filtrar
            </button>
        </div>
    </form>

    {{-- Resumen --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total ventas</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary->total_count, 0) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Monto recaudado</p>
            <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">S/ {{ number_format($summary->total_amount, 2) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Ticket promedio</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">S/ {{ $summary->total_count ? number_format($summary->total_amount / $summary->total_count, 2) : '0.00' }}</p>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">#</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Comprobante</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($sales as $sale)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">#{{ $sale->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $sale->updated_at?->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            @php
                                $labels = ['pos' => ['Salón', 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200'], 'delivery' => ['Delivery', 'bg-teal-100 text-teal-800 dark:bg-teal-900/50 dark:text-teal-200'], 'quick_sale' => ['Venta rápida', 'bg-fuchsia-100 text-fuchsia-800 dark:bg-fuchsia-900/50 dark:text-fuchsia-200']];
                                $label = $labels[$sale->sale_type] ?? ['—', 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'];
                            @endphp
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $label[1] }}">{{ $label[0] }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                            {{ $sale->clientable?->social_reason ?: $sale->clientable?->name ?: 'Público general' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            @if ($sale->series && $sale->number)
                                {{ $sale->series }}-{{ $sale->number }}
                            @else
                                {{ $sale->documentType?->name ?? '—' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">S/ {{ number_format($sale->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No hay ventas para los filtros indicados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sales->links() }}
    </div>
@endsection