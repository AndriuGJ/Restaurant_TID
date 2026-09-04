<header class="sticky top-0 z-20 border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-slate-950">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6">
        <div class="flex items-center gap-3">
            <button type="button" data-open-sidebar
                class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-brand-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-brand-400 lg:hidden"
                aria-label="Abrir menú">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500 text-lg shadow-sm shadow-brand-500/30"><img src="{{ asset('assets/img/logo.png') }}"></span>
                <span class="font-display text-2xl font-normal tracking-wide text-gray-900 dark:text-white">Restaurante</span>
            </a>
        </div>

        <div class="flex items-center gap-3 sm:gap-4">
            <div class="flex items-center gap-2.5">
                <span
                    class="hidden h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700 dark:bg-brand-500/20 dark:text-brand-300 sm:flex">
                    {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                </span>
                <span class="hidden text-sm font-medium text-gray-700 dark:text-gray-300 sm:inline">
                    {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                </span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 transition hover:border-brand-300 hover:text-brand-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-brand-500 dark:hover:text-brand-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </div>
</header>
