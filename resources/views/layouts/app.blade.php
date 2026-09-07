<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Restaurante'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased">
    <div class="flex min-h-full flex-col">
        @include('_partials.header')

        <div class="flex flex-1">
            <div id="sidebar-overlay" data-sidebar-overlay
                class="fixed inset-0 z-30 hidden bg-gray-900/50 lg:hidden"></div>

            <aside id="mobile-sidebar"
class="fixed inset-y-0 left-0 z-40 flex w-64 max-w-[85vw] -translate-x-full flex-col border-r border-slate-800 bg-slate-900 transition-transform duration-300 lg:hidden">
                <div class="flex h-16 items-center justify-between border-b border-slate-800 px-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-500 text-base"><img src="{{ asset('assets/img/logo.png') }}"></span>
                        <span class="font-display text-xl font-normal tracking-wide text-white">Restaurante</span>
                    </a>
                    <button type="button" data-close-sidebar
                        class="rounded-lg p-2 text-gray-400 hover:bg-slate-800 hover:text-white"
                        aria-label="Cerrar menú">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 overflow-y-auto p-4">
                    @include('_partials.sidebar-menu')
                </nav>
            </aside>

            @include('_partials.sidebar')

            <main class="flex-1 p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-800">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        @include('_partials.footer')
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.querySelector('[data-sidebar-overlay]');
            if (!sidebar || !overlay) return;

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }

            document.querySelectorAll('[data-open-sidebar]').forEach(function (btn) {
                btn.addEventListener('click', openSidebar);
            });
            document.querySelectorAll('[data-close-sidebar]').forEach(function (btn) {
                btn.addEventListener('click', closeSidebar);
            });

            overlay.addEventListener('click', closeSidebar);
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeSidebar();
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1024) closeSidebar();
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
