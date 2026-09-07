@extends('layouts.app')

@section('title', 'Cajas — Apertura y cierre')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cajas</h1>
            <p class="mt-1 text-sm text-gray-600">Apertura y cierre de sesiones de caja.</p>
        </div>
        @can('cajas-abrir')
            <a href="{{ route('cash-registers.sessions.create') }}"
                class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Abrir caja
            </a>
        @endcan
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Caja</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Turno</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Abierto por</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Apertura</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Cierre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Fechas</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($sessions as $session)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $session->cashRegister->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $session->shift->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $session->userOpening->name }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600">S/ {{ number_format($session->opening_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600">
                            {{ $session->closing_amount !== null ? 'S/ '.number_format($session->closing_amount, 2) : '—' }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $open = $session->status === 'open';
                            @endphp
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                {{ $open ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $open ? 'Abierta' : 'Cerrada' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $session->opened_at?->format('d/m/Y H:i') }}
                            @if ($session->closed_at)
                                <br>{{ $session->closed_at->format('d/m/Y H:i') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if ($open)
                                @can('cajas-cerrar')
                                    <a href="{{ route('cash-registers.sessions.edit', $session) }}"
                                        class="rounded-md bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-amber-500">
                                        Cerrar
                                    </a>
                                @endcan
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">
                            No hay sesiones de caja registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sessions->links() }}
    </div>
@endsection