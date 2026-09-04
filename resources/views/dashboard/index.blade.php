@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8 flex flex-col gap-1">
        <h1 class="text-xl font-bold text-gray-900">
            Buen día, {{ auth()->user()->first_name }} 👋
        </h1>
        <p class="text-sm text-gray-600">
            {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }} — Resumen de tu restaurante hoy.
        </p>
    </div>

    {{-- Tarjetas de estadísticas --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Ventas de hoy --}}
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Ventas hoy</p>
                <span class="flex h-10 w-10 items-center justify-center rounded bg-brand-500/15 text-brand-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 0 3 6h18a3.75 3.75 0 0 1 0 7.5h-3.375c-.621 0-1.125.504-1.125 1.125v3.75" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                S/ {{ number_format($stats['salesTodayTotal'], 2, '.', ',') }}
            </p>
            <p class="mt-1 text-xs text-gray-500">
                {{ $stats['salesToday'] }} comprobante{{ $stats['salesToday'] === 1 ? '' : 's' }} cobrado{{ $stats['salesToday'] === 1 ? '' : 's' }}
            </p>
        </div>

        {{-- Órdenes en cocina --}}
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">En cocina</p>
                <span class="flex h-10 w-10 items-center justify-center rounded bg-amber-500/15 text-amber-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">{{ $stats['kitchenPending'] }}</p>
            <p class="mt-1 text-xs text-gray-500">órdenes pendientes o en preparación</p>
        </div>

        {{-- Productos con stock bajo --}}
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Stock bajo</p>
                <span class="flex h-10 w-10 items-center justify-center rounded bg-red-500/15 text-red-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold tracking-tight text-gray-900">{{ $stats['lowStockCount'] }}</p>
            <p class="mt-1 text-xs text-gray-500">productos por reponer (≤ 10)</p>
        </div>

        {{-- Estado de caja --}}
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Caja</p>
                <span class="flex h-10 w-10 items-center justify-center rounded bg-emerald-500/15 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 0 3 6h18a3.75 3.75 0 0 1 0 7.5h-3.375c-.621 0-1.125.504-1.125 1.125v3.5" />
                    </svg>
                </span>
            </div>
            @if ($stats['openCashSession'])
                <p class="mt-3 text-3xl font-bold tracking-tight text-emerald-600">Abierta</p>
                <p class="mt-1 text-xs text-gray-500">
                    Apertura S/ {{ number_format($stats['openCashSession']->opening_amount, 2, '.', ',') }}
                </p>
            @else
                <p class="mt-3 text-3xl font-bold tracking-tight text-gray-300">Cerrada</p>
                <p class="mt-1 text-xs text-gray-500">
                    Abre una caja para vender en el POS
                </p>
            @endif
        </div>
    </div>

    {{-- Accesos directos --}}
    <h2 class="mt-10 flex items-center gap-2 text-base font-semibold text-gray-900">
        <svg class="h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
        </svg>
        Accesos rápidos
    </h2>
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($shortcuts as $shortcut)
            <a href="{{ route($shortcut['route']) }}"
                class="group flex items-center gap-4 rounded-md border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md">
                <x-dashboard-icon :icon="$shortcut['icon']" class="h-6 w-6 text-brand-500" />
                <div class="min-w-0">
                    <p class="truncate font-semibold text-gray-900 group-hover:text-brand-600">
                        {{ $shortcut['label'] }}
                    </p>
                    <p class="truncate text-xs text-gray-500">{{ $shortcut['desc'] }}</p>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Productos con stock bajo --}}
    <h2 class="mt-10 flex items-center gap-2 text-base font-semibold text-gray-900">
        <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
        </svg>
        Productos por reponer
    </h2>
    <div class="mt-4 overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
        @if ($stats['lowStockProducts']->isEmpty())
            <p class="p-6 text-sm text-gray-500">
                No hay productos con stock bajo. Todo está en orden. 🎉
            </p>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($stats['lowStockProducts'] as $product)
                    <li class="flex items-center justify-between px-5 py-3.5">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-gray-900">{{ $product->name }}</p>
                            <p class="text-xs text-gray-500">{{ $product->unit_of_measure }}</p>
                        </div>
                        <span class="ml-4 shrink-0 rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-600">
                            {{ $product->stock }} restantes
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection