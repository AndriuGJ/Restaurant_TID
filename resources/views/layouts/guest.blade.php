<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Restaurante'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col bg-gray-950 antialiased">
    <div class="flex min-h-[100dvh] flex-1 flex-col lg:flex-row lg:items-stretch">

        {{-- Panel de marca (izquierda, solo en pantallas grandes) --}}
        <div class="relative hidden overflow-hidden bg-brand-600 lg:flex lg:w-[44%] lg:flex-col lg:justify-between lg:p-12">
            {{-- Imagen de fondo con capa oscura para que el texto blanco se lea --}}
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/img/restaurant_fondo.jpg');"></div>
            <div class="absolute inset-0 bg-gray-950/70"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-950/80 via-transparent to-brand-950/40"></div>

            <div class="relative">
                <div class="flex items-center gap-3">
                    @if ($company?->logo)
                        <img src="{{ asset('storage/'.$company->logo) }}"
                            alt="{{ $company->commercial_name ?: $company->name }}"
                            class="h-14 w-14 rounded-md bg-white/90 object-contain p-1 shadow-lg">
                    @else
                        <span class="flex h-14 w-14 items-center justify-center rounded-md bg-white/10 text-3xl backdrop-blur-sm"><img src="{{ asset('assets/img/logo.png') }}"></span>
                    @endif
                    <div>
                        <span class="block font-display text-4xl font-normal tracking-wide text-white">
                            {{ $company?->commercial_name ?: ($company?->name ?: 'RESTAURANTE') }}
                        </span>
                        @if ($company?->name && $company->commercial_name)
                            <span class="text-sm text-brand-100">{{ $company->name }}</span>
                        @endif
                    </div>
                </div>
                <p class="mt-6 max-w-md text-xl font-medium leading-relaxed text-white/90">
                    Sistema nomas
                </p>
            </div>

            <div class="relative flex items-center gap-10">
                <div>
                    <p class="font-display text-3xl font-normal text-white">Punto de venta</p>
                    <p class="text-sm text-white/80">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quibusdam est magnam sequi nesciunt, rerum voluptatibus officia iure maiores obcaecati quidem quod neque minima ducimus autem cumque ex veritatis quia expedita?.</p>
                </div>
                <div class="h-10 w-px bg-white/30"></div>
                <div>
                    <p class="font-display text-3xl font-normal text-white">Control total</p>
                    <p class="text-sm text-white/80">Cocina, caja e inventario conectados.</p>
                </div>
            </div>
        </div>

        {{-- Columna de autenticación (derecha) --}}
        <div class="flex flex-1 flex-col items-center justify-center bg-gray-950 px-6 py-12 sm:px-8 lg:bg-gray-100">
            <div class="w-full max-w-md">
                @yield('content')
            </div>

            <p class="mt-8 text-center text-xs text-gray-400 lg:text-gray-400">
                Sistema de Gestión de Restaurante &copy; {{ date('Y') }}
            </p>
        </div>
    </div>
</body>
</html>
