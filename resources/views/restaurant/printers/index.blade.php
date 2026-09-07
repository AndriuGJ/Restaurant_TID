@extends('layouts.app')

@section('title', 'Impresoras')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Impresoras</h1>
            <p class="mt-1 text-sm text-gray-600">Registra las impresoras térmicas de la red y elige la caja principal.</p>
        </div>
        @can('configuracion-editar')
            <a href="{{ route('restaurant.printers.create') }}"
                class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Nueva impresora
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Conexión</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Dirección</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Impresión</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($printers as $printer)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $printer->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if (($printer->connection_type ?? 'network') === 'local')
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700">Cable / USB</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">Red</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if (($printer->connection_type ?? 'network') === 'local')
                                <span>{{ $printer->queue_name ?? '—' }}</span>
                                <span class="ml-2 inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700">
                                    {{ $printer->send_raw ? 'Térmica' : 'PDF' }}
                                </span>
                            @else
                                {{ $printer->ip_address }}:{{ $printer->port }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($printer->is_default)
                                <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-700">
                                    Caja principal
                                </span>
                            @else
                                <span class="text-sm text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @include('_partials.status-badge', ['active' => $printer->is_active])
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-3">
                                @can('configuracion-editar')
                                    <form method="POST" action="{{ route('restaurant.printers.test', $printer) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="text-sm font-medium text-gray-600 hover:text-gray-500">
                                            Probar
                                        </button>
                                    </form>
                                    @include('_partials.row-actions', [
                                        'editUrl' => route('restaurant.printers.edit', $printer),
                                        'deleteUrl' => route('restaurant.printers.destroy', $printer),
                                        'deleteLabel' => 'esta impresora',
                                    ])
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                            No hay impresoras registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $printers->links() }}
    </div>
@endsection