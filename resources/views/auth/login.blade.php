@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
    {{-- Encabezado móvil --}}
    <div class="mb-8 flex items-center gap-3 lg:hidden">
        <span class="flex h-12 w-12 items-center justify-center rounded-md bg-brand-500 text-2xl shadow-lg shadow-brand-500/30">🍽️</span>
        <div>
            <h1 class="font-display text-3xl font-normal tracking-wide text-white">RESTAURANTE</h1>
            <p class="text-xs text-gray-400">Sistema de Gestión</p>
        </div>
    </div>

    {{-- Encabezado desktop --}}
    <div class="mb-6 hidden lg:block">
        <h1 class="mt-5 font-display text-6xl font-normal tracking-wide text-gray-900">Bienvenido</h1>
        <p class="mt-2 text-gray-500">Inicia sesión para acceder al sistema.</p>
    </div>

    <div class="overflow-hidden rounded-md border border-gray-200 bg-white shadow-xl shadow-gray-900/5">
        <form method="POST" action="{{ route('login') }}" class="space-y-6 p-8">
            @csrf

            <div>
                <label for="login" class="block text-sm font-semibold text-gray-700">
                    Usuario o correo
                </label>
                <div class="relative mt-1.5">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </span>
                    <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus
                        placeholder="tu@correo.com o tu usuario"
                        class="block w-full rounded-md border-gray-300 py-2.5 pl-10 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700">
                    Contraseña
                </label>
                <div class="relative mt-1.5">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </span>
                    <input id="password" name="password" type="password" required placeholder="••••••••"
                        class="block w-full rounded-md border-gray-300 py-2.5 pl-10 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>

            @if ($errors->any())
                <div class="flex items-start gap-3 rounded-md bg-red-50 p-3.5 text-sm text-red-700">
                    <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <span class="font-medium">{{ $errors->first() }}</span>
                </div>
            @endif

            <div>
                <button type="submit"
                    class="group flex w-full items-center justify-center gap-2 rounded-md bg-brand-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                    Iniciar sesión
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
@endsection
