@extends('layouts.app')

@section('title', 'Punto de Venta — Requiere caja abierta')

@section('content')
    <div class="mx-auto mt-16 max-w-lg rounded-md border border-gray-200 bg-white p-8 text-center shadow-sm">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-100">
            <svg class="h-7 w-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v3m0 4h.01M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2z" />
            </svg>
        </div>
        <h1 class="mt-4 text-xl font-bold text-gray-900">Caja no abierta</h1>
        <p class="mt-2 text-sm text-gray-600">
            Para usar el punto de venta es necesario tener una <strong>caja abierta</strong>. Todas las ventas se
            registran en la sesión de caja activa hasta que esta se cierre.
        </p>
        <div class="mt-6 flex flex-col items-center gap-3">
            @can('cajas-abrir')
                <a href="{{ route('cash-registers.sessions.create') }}"
                    class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                    Abrir caja
                </a>
            @endcan
            @can('cajas-ver')
                <a href="{{ route('cash-registers.sessions.index') }}"
                    class="text-sm font-medium text-gray-600 hover:text-gray-500">
                    Ver sesiones de caja
                </a>
            @endcan
            <a href="{{ route('pos.hall') }}"
                class="text-sm font-medium text-gray-600 hover:text-gray-500">
                Volver al salón
            </a>
        </div>
    </div>
@endsection