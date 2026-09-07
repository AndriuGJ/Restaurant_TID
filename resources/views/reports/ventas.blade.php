@extends('layouts.app')

@section('title', 'Reporte de Ventas')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reporte de Ventas</h1>
            <p class="mt-1 text-sm text-gray-600">Resumen y detalle de ventas (salón, delivery y venta rápida) en un rango de fechas.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" data-open-preview
                class="inline-flex items-center gap-1.5 rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:border-brand-300 hover:text-brand-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M18 6.75h.008v.008H18V6.75zM10.5 7.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm8.25 13.5H3.75a1.5 1.5 0 01-1.5-1.5V5.25a1.5 1.5 0 011.5-1.5h16.5a1.5 1.5 0 011.5 1.5v14.25a1.5 1.5 0 01-1.5 1.5z" />
                </svg>
                Vista previa
            </button>
            <a href="{{ route('reportes.ventas.export', request()->query()) }}"
                class="inline-flex items-center gap-1.5 rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Exportar PDF
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('reportes.ventas') }}" class="mt-6 grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Desde</label>
            <input type="date" name="from" value="{{ $from }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Hasta</label>
            <input type="date" name="to" value="{{ $to }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Tipo de venta</label>
            <select name="sale_type"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Todos</option>
                <option value="pos" @selected($saleType === 'pos')>Salón</option>
                <option value="delivery" @selected($saleType === 'delivery')>Delivery</option>
                <option value="quick_sale" @selected($saleType === 'quick_sale')>Venta rápida</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Comprobante</label>
            <select name="document_type_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Todos</option>
                @foreach ($documentTypes as $documentType)
                    <option value="{{ $documentType->id }}" @selected($documentTypeId == $documentType->id)>{{ $documentType->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-4">
            <button type="submit"
                class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700">
                Filtrar
            </button>
        </div>
    </form>

    {{-- Resumen --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total ventas</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($summary->total_count, 0) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Monto recaudado</p>
            <p class="mt-1 text-2xl font-bold text-green-600">S/ {{ number_format($summary->total_amount, 2) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Ticket promedio</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">S/ {{ $summary->total_count ? number_format($summary->total_amount / $summary->total_count, 2) : '0.00' }}</p>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">#</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Comprobante</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($sales as $sale)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-600">#{{ $sale->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $sale->updated_at?->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            @php
                                $labels = ['pos' => ['Salón', 'bg-brand-100 text-brand-700'], 'delivery' => ['Delivery', 'bg-teal-100 text-teal-800'], 'quick_sale' => ['Venta rápida', 'bg-fuchsia-100 text-fuchsia-800']];
                                $label = $labels[$sale->sale_type] ?? ['—', 'bg-gray-100 text-gray-800'];
                            @endphp
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $label[1] }}">{{ $label[0] }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800">
                            {{ $sale->clientable?->social_reason ?: $sale->clientable?->name ?: 'Público general' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if ($sale->series && $sale->number)
                                {{ $sale->series }}-{{ $sale->number }}
                            @else
                                {{ $sale->documentType?->name ?? '—' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">S/ {{ number_format($sale->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No hay ventas para los filtros indicados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sales->links() }}
    </div>

    <div id="preview-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 p-4">
        <div class="flex h-full w-full max-w-4xl flex-col overflow-hidden rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                <h2 class="text-lg font-semibold text-gray-900">Vista previa del reporte</h2>
                <div class="flex items-center gap-2">
                    <a id="preview-download" href="{{ route('reportes.ventas.export', request()->query()) }}"
                        class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                        Descargar PDF
                    </a>
                    <button type="button" data-modal-close
                        class="rounded-md p-1 text-gray-400 transition hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex-1 bg-gray-100">
                <iframe id="preview-frame" class="h-full w-full" src="" title="Vista previa del reporte"></iframe>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const modal = document.getElementById('preview-modal');
            const frame = document.getElementById('preview-frame');
            const openBtn = document.querySelector('[data-open-preview]');
            if (!modal || !frame || !openBtn) return;

            const open = () => {
                frame.src = @json(route('reportes.ventas.preview', request()->query()));
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };
            const close = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                frame.src = '';
            };

            openBtn.addEventListener('click', open);
            modal.querySelectorAll('[data-modal-close]').forEach((btn) => btn.addEventListener('click', close));
            modal.addEventListener('click', (e) => {
                if (e.target === modal) close();
            });
        })();
    </script>
@endpush