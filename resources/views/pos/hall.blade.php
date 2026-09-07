@extends('layouts.app')

@section('title', 'Punto de Venta')

@section('content')
    <div class="flex flex-col gap-5">
        {{-- Encabezado --}}
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Punto de Venta · Salón</h1>
                <p class="mt-1 text-sm text-gray-600">Arrastra las mesas para ubicarlas. Haz clic en una mesa para ver sus opciones (venta o reserva).</p>
            </div>

            {{-- Barra de acciones --}}
            <div class="flex flex-wrap items-center gap-2 rounded-md border border-gray-200 bg-white p-2 shadow-sm">
                <label class="flex items-center gap-2 px-1 text-sm font-medium text-gray-700">
                    <svg class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                    </svg>
                    Salón
                </label>
                <select id="hall-selector"
                    class="rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    @foreach ($halls as $hall)
                        <option value="{{ $hall->id }}" @selected($activeHall && $hall->id === $activeHall->id)>{{ $hall->name }}</option>
                    @endforeach
                </select>

                <div class="mx-1 h-6 w-px bg-gray-200"></div>

                @can('pos-delivery')
                    <a href="{{ route('pos.new.delivery') }}"
                        class="inline-flex items-center gap-1.5 rounded-md bg-teal-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        Delivery
                    </a>
                @endcan
                @can('pos-venta-rapida')
                    <a href="{{ route('pos.new.quick-sale') }}"
                        class="inline-flex items-center gap-1.5 rounded-md bg-fuchsia-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-fuchsia-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.024a2.993 2.993 0 0 0 2.25 1.024c.896 0 1.7-.393 2.25-1.024a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72m-13.5 8.65h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                        </svg>
                        Venta rápida
                    </a>
                @endcan
                @can('pos-preparacion')
                    <a href="{{ route('pos.kitchen.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-md bg-orange-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                        </svg>
                        Cocina
                    </a>
                @endcan
            </div>
        </div>

        {{-- Leyenda --}}
        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 rounded-md border border-gray-200 bg-white px-4 py-2.5 text-xs text-gray-600 shadow-sm">
            <span class="font-medium uppercase tracking-wide text-gray-500">Mesas</span>
            <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full bg-emerald-500"></span> Disponible</span>
            <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full bg-amber-500"></span> Ocupada</span>
            <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full bg-sky-500"></span> Reservada</span>
            <span class="ml-auto hidden items-center gap-1.5 text-gray-400 sm:inline-flex">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                </svg>
                Arrastra para mover · clic para opciones · clic derecho para venta
            </span>
        </div>

        {{-- Canvas --}}
        <div class="relative overflow-auto rounded-md border border-gray-200 bg-gray-50/60"
            style="height: 560px;
                background-image: radial-gradient(rgba(0,0,0,0.07) 1px, transparent 1px);
                background-size: 24px 24px;">
            <div id="pos-canvas" class="relative" style="width: 1100px; min-height: 560px;">
                @forelse ($activeHall?->tables ?? [] as $table)
                    @include('pos._table-node', ['table' => $table])
                @empty
                    <div class="flex h-[560px] flex-col items-center justify-center gap-2 text-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-gray-200">
                            <svg class="h-7 w-7 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500">Este salón no tiene mesas</p>
                        <a href="{{ route('restaurant.tables.create') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500">
                            Agregar mesas
                        </a>
                    </div>
                @endforelse
                <div id="table-context-menu"
                    class="absolute z-20 hidden w-48 overflow-hidden rounded-md border border-gray-200 bg-white shadow-lg">
                <button type="button" data-menu-open-sale
                    class="flex w-full items-center gap-2.5 px-3 py-2.5 text-left text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-brand-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V13.5Zm0 2.25H8.25v.008H8.25V15.75Zm6.75-2.25h.008v.008H15V13.5Zm0 2.25h.008v.008H15V15.75Zm-6.75 3h.008v.008H8.25v-.008Zm6.75 0h.008v.008H15v-.008Zm-3.75-5.25h.008v.008H11.25v-.008Zm0 2.25h.008v.008H11.25v-.008Zm0 2.25h.008v.008H11.25v-.008ZM4.5 21h15M7.5 6.75h9M7.5 6.75a1.5 1.5 0 0 0-1.5-1.5H4.5m13.5 1.5a1.5 1.5 0 0 1 1.5-1.5h1.5m-4.5 0V3.75A1.5 1.5 0 0 0 15 2.25H9.75a1.5 1.5 0 0 0-1.5 1.5v3m6.75 0h.008v.008H15V6.75Z" />
                    </svg>
                    Realizar venta
                </button>
                <button type="button" data-menu-reserve
                    class="hidden flex w-full items-center gap-2.5 border-t border-gray-100 px-3 py-2.5 text-left text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-brand-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-6.75-6h.008v.008h-.008V12Zm0 2.25h.008v.008h-.008V14.25Zm0 2.25h.008v.008h-.008V16.5Z" />
                    </svg>
                    Reservar mesa
                </button>
                <button type="button" data-menu-cancel-reserve
                    class="hidden flex w-full items-center gap-2.5 border-t border-gray-100 px-3 py-2.5 text-left text-sm font-medium text-red-600 transition hover:bg-red-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75v3.75m-3.75 6.75h7.5a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    Cancelar reserva
                </button>
            </div>
        </div>
    </div>

    <div id="reserve-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-gray-900/50 p-4">
        <div class="w-full max-w-sm rounded-lg bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Reservar mesa</h2>
                <button type="button" data-modal-close
                    class="rounded-md p-1 text-gray-400 transition hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <p id="reserve-modal-table" class="mt-0.5 text-sm text-gray-600"></p>
            <form id="reserve-form" class="mt-4 space-y-4">
                <div>
                    <label for="reserve-name" class="block text-sm font-medium text-gray-700">A nombre de quién</label>
                    <input id="reserve-name" name="customer_name" type="text" autocomplete="off"
                        placeholder="Nombre del cliente"
                        class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label for="reserve-people" class="block text-sm font-medium text-gray-700">¿Cuántas personas son?</label>
                    <input id="reserve-people" name="people_count" type="number" min="1" max="99" value="2"
                        class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div id="reserve-error" class="hidden rounded-md bg-red-50 p-3 text-sm text-red-800"></div>
                <div class="flex items-center justify-end gap-2 pt-1">
                    <button type="button" data-modal-close
                        class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-600">
                        Confirmar reserva
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="pos-error" class="hidden rounded-md bg-red-50 p-3 text-sm text-red-800"></div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const nodes = [...document.querySelectorAll('[data-table-node]')];
            const errorBox = document.getElementById('pos-error');
            const selector = document.getElementById('hall-selector');

            const menu = document.getElementById('table-context-menu');
            const menuOpenSale = menu.querySelector('[data-menu-open-sale]');
            const menuReserve = menu.querySelector('[data-menu-reserve]');
            const menuCancel = menu.querySelector('[data-menu-cancel-reserve]');

            const modal = document.getElementById('reserve-modal');
            const modalTable = document.getElementById('reserve-modal-table');
            const reserveForm = document.getElementById('reserve-form');
            const reserveError = document.getElementById('reserve-error');

            let currentNode = null;
            let currentReserveUrl = null;

            const showError = (message) => {
                errorBox.textContent = message;
                errorBox.classList.remove('hidden');
            };

            const hideMenu = () => {
                menu.classList.add('hidden');
                currentNode = null;
            };

            const showMenu = (node, e) => {
                currentNode = node;
                const canvas = document.getElementById('pos-canvas');
                const canvasRect = canvas.getBoundingClientRect();
                const status = node.dataset.status;
                const hasReservation = node.dataset.hasReservation === '1';

                menuReserve.classList.toggle('hidden', status === 'occupied' || (status === 'reserved' && hasReservation));
                menuCancel.classList.toggle('hidden', !(status === 'reserved' && hasReservation));

                menu.classList.remove('hidden');
                menu.style.left = `${Math.min(Math.max(4, e.clientX - canvasRect.left), canvasRect.width - menu.offsetWidth - 4)}px`;
                menu.style.top = `${Math.min(Math.max(4, e.clientY - canvasRect.top), canvasRect.height - menu.offsetHeight - 4)}px`;
            };

            const openReserveModal = (node) => {
                currentReserveUrl = node.dataset.reserveUrl;
                modalTable.textContent = `Mesa ${node.dataset.tableName}`;
                document.getElementById('reserve-name').value = '';
                document.getElementById('reserve-people').value = 2;
                reserveError.classList.add('hidden');
                modal.classList.remove('hidden');
            };

            const closeReserveModal = () => modal.classList.add('hidden');

            nodes.forEach((node) => {
                let dragged = false;
                let startX = 0;
                let startY = 0;
                let offsetX = 0;
                let offsetY = 0;

                node.addEventListener('pointerdown', (e) => {
                    dragged = false;
                    startX = e.clientX;
                    startY = e.clientY;
                    const rect = node.getBoundingClientRect();
                    offsetX = e.clientX - rect.left;
                    offsetY = e.clientY - rect.top;
                    node.setPointerCapture(e.pointerId);
                });

                node.addEventListener('pointermove', (e) => {
                    if (!node.hasPointerCapture(e.pointerId)) return;
                    const dx = e.clientX - startX;
                    const dy = e.clientY - startY;
                    if (Math.abs(dx) > 4 || Math.abs(dy) > 4) {
                        dragged = true;
                    }
                    if (!dragged) return;

                    const canvas = document.getElementById('pos-canvas');
                    const canvasRect = canvas.getBoundingClientRect();
                    let x = e.clientX - canvasRect.left - offsetX;
                    let y = e.clientY - canvasRect.top - offsetY;
                    x = Math.max(0, Math.min(x, canvasRect.width - node.offsetWidth));
                    y = Math.max(0, Math.min(y, canvasRect.height - node.offsetHeight));
                    node.style.left = x + 'px';
                    node.style.top = y + 'px';
                });

                node.addEventListener('pointerup', (e) => {
                    node.releasePointerCapture(e.pointerId);
                    if (dragged) {
                        const canvas = document.getElementById('pos-canvas');
                        const canvasRect = canvas.getBoundingClientRect();
                        const x = Math.round(e.clientX - canvasRect.left - offsetX);
                        const y = Math.round(e.clientY - canvasRect.top - offsetY);

                        fetch(node.dataset.moveUrl, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                pos_x: Math.max(0, x),
                                pos_y: Math.max(0, y),
                            }),
                        }).then((res) => {
                            if (!res.ok) throw new Error('No se pudo guardar la posición');
                        }).catch((err) => showError(err.message));
                        return;
                    }

                    if (e.button !== 0) return;
                    hideMenu();
                    showMenu(node, e);
                });

                node.addEventListener('contextmenu', (e) => {
                    e.preventDefault();
                    hideMenu();
                    window.location.href = node.dataset.openUrl;
                });
            });

            menuOpenSale.addEventListener('click', () => {
                if (currentNode) {
                    window.location.href = currentNode.dataset.openUrl;
                }
            });

            menuReserve.addEventListener('click', () => {
                if (!currentNode) return;
                const node = currentNode;
                hideMenu();
                openReserveModal(node);
            });

            menuCancel.addEventListener('click', () => {
                if (!currentNode) return;
                const tableName = currentNode.dataset.tableName;
                hideMenu();
                if (!window.confirm(`¿Cancelar la reserva de la mesa ${tableName}?`)) return;
                fetch(currentNode.dataset.cancelReserveUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                }).then((res) => {
                    if (!res.ok) throw new Error('No se pudo cancelar la reserva');
                    window.location.reload();
                }).catch((err) => showError(err.message));
            });

            document.querySelectorAll('[data-modal-close]').forEach((btn) => {
                btn.addEventListener('click', closeReserveModal);
            });

            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeReserveModal();
            });

            reserveForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                reserveError.classList.add('hidden');
                const body = new FormData(reserveForm);

                try {
                    const res = await fetch(currentReserveUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body,
                    });

                    if (res.ok) {
                        window.location.reload();
                        return;
                    }

                    let message = 'No se pudo guardar la reserva.';
                    try {
                        const data = await res.json();
                        message = Object.values(data.errors || {}).flat()[0] || message;
                    } catch { /* respuesta sin JSON */ }
                    reserveError.textContent = message;
                    reserveError.classList.remove('hidden');
                } catch (err) {
                    reserveError.textContent = err.message;
                    reserveError.classList.remove('hidden');
                }
            });

            document.addEventListener('click', (e) => {
                if (e.target.closest('#table-context-menu') || e.target.closest('[data-table-node]')) return;
                hideMenu();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    hideMenu();
                    closeReserveModal();
                }
            });

            selector.addEventListener('change', () => {
                window.location.href = `{{ route('pos.hall') }}?hall=${selector.value}`;
            });
        })();
    </script>
@endpush