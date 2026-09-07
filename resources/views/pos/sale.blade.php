@extends('layouts.app')

@section('title', 'Pedido')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pedido #{{ $sale->id }}</h1>
            <p class="mt-1 text-sm text-gray-600">
                @if ($sale->sale_type === 'delivery')
                    <span class="rounded-full bg-teal-100 px-2 py-0.5 text-xs font-semibold text-teal-700">DELIVERY</span>
                @elseif ($sale->sale_type === 'quick_sale')
                    <span class="rounded-full bg-fuchsia-100 px-2 py-0.5 text-xs font-semibold text-fuchsia-700">PARA
                        LLEVAR</span>
                @else
                    Mesa <span class="font-semibold">{{ $sale->table?->name ?? '—' }}</span>
                @endif
                · {{ $sale->details_count }} productos
                · Estado: {{ strtoupper($sale->status) }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pos.hall') }}" class="text-sm font-medium text-gray-600 hover:text-gray-500">← Volver al
                salón</a>
            <form method="POST" action="{{ route('pos.sale.cancel', $sale) }}" class="inline">
                @csrf
                @method('DELETE')
                <button id="btn-cancelar" type="submit"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-xs transition-colors hover:bg-red-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 active:bg-red-800">
                    Cancelar Pedido
                </button>
            </form>
        </div>
    </div>

    @if ($sale->sale_type === 'delivery')
        <div class="mt-6 rounded-md border border-teal-200 bg-white p-4 shadow-sm">
            <form method="POST" action="{{ route('pos.sale.delivery', $sale) }}"
                class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-gray-700">Proveedor de delivery</label>
                    <select name="delivery_provider_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        <option value="">Personal propio</option>
                        @foreach ($deliveryProviders as $provider)
                            <option value="{{ $provider->id }}" @selected($provider->id === $sale->delivery_provider_id)>{{ $provider->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Persona / repartidor</label>
                    <input type="text" name="delivery_person_name"
                        value="{{ old('delivery_person_name', $sale->delivery_person_name) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full rounded-md bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-500">
                        Guardar delivery
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-5">
        {{-- Catálogo de productos --}}
        <div class="lg:col-span-3">
            <div class="rounded-md border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <input id="pos-search" type="text" placeholder="Buscar producto..."
                        class="rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    <div class="flex flex-wrap gap-2" id="pos-categories">
                        <button type="button" data-cat="all"
                            class="rounded-full bg-brand-500 px-3 py-1 text-xs font-semibold text-white">Todos</button>
                        @foreach ($categories as $category)
                            <button type="button" data-cat="{{ $category->id }}"
                                class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-200">{{ $category->name }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="grid max-h-[480px] grid-cols-2 gap-3 overflow-y-auto sm:grid-cols-3" id="pos-products">
                    @foreach ($products as $product)
                        <button type="button" data-product="{{ $product->id }}"
                            data-cat="{{ $product->product_category_id ?? '' }}" data-name="{{ $product->name }}"
                            class="pos-product rounded-md border border-gray-200 p-2 text-left shadow-sm hover:border-brand-400 hover:shadow">
                            @if ($product->image_url)
                                <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}"
                                    loading="lazy" class="mb-2 h-24 w-full rounded-md object-cover">
                            @else
                                <div
                                    class="mb-2 flex h-24 w-full items-center justify-center rounded-md bg-gray-100 text-gray-400">
                                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 6h16M4 6a2 2 0 00-2 2v8a2 2 0 002 2v0a2 2 0 002-2" />
                                    </svg>
                                </div>
                            @endif
                            <p class="truncate text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                            <p class="mt-1 text-sm font-bold text-brand-600">S/
                                {{ number_format($product->sale_price, 2) }}</p>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Detalle del pedido --}}
        <div class="lg:col-span-2">
            <div class="rounded-md border border-gray-200 bg-white p-4 shadow-sm">
                <div id="pos-cart" class="mb-4 hidden rounded-md bg-gray-50 p-3">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-900">Selección</p>
                        <div class="flex items-center gap-3">
                            <span id="pos-cart-count" class="text-xs font-medium text-gray-500"></span>
                            <button type="button" id="pos-cart-clear"
                                class="text-xs font-medium text-red-600 hover:text-red-500">Vaciar</button>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Toca los productos para irlos guardando. Ajusta cantidad y nota, y al final dale
                        "Agregar al pedido".
                    </p>
                    <ul id="pos-cart-items" class="mt-3 space-y-3"></ul>
                    <button type="button" id="pos-cart-submit"
                        class="mt-3 w-full rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                        Agregar al pedido
                    </button>
                </div>

                <form id="pos-bulk-hidden" method="POST" action="{{ route('pos.sale.add-products', $sale) }}"
                    class="hidden">
                    @csrf
                </form>

                <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Detalle</h2>
                <ul class="mt-3 space-y-3" id="pos-detail">
                    @foreach ($sale->details as $detail)
                        <li class="flex items-start justify-between border-b border-gray-100 pb-2">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $detail->product?->name }}</p>
                                @if ($detail->notes)
                                    <p class="mt-0.5 text-xs text-gray-500">"{{ $detail->notes }}"</p>
                                @endif
                                <p class="mt-0.5 text-xs text-gray-500">
                                    {{ format_quantity($detail->quantity) }} × S/ {{ number_format($detail->unit_price, 2) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <form method="POST" action="{{ route('pos.sale.detail.update', $detail) }}"
                                    class="pos-detail-qty flex items-center gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <div
                                        class="flex items-center overflow-hidden rounded-md border border-gray-300 shadow-sm">
                                        <button type="button" data-qty-minus aria-label="Restar una unidad"
                                            class="flex h-8 w-8 items-center justify-center text-gray-600 transition hover:bg-gray-100 hover:text-brand-600 active:bg-gray-200">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                            </svg>
                                        </button>
                                        <input type="number" name="quantity" value="{{ format_quantity($detail->quantity) }}"
                                            min="1" step="1"
                                            class="h-8 w-10 border-x border-gray-300 text-center text-sm font-semibold text-gray-900 [-moz-appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                        <button type="button" data-qty-plus aria-label="Sumar una unidad"
                                            class="flex h-8 w-8 items-center justify-center bg-brand-500 text-white transition hover:bg-brand-600 active:bg-brand-700">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </button>
                                    </div>
                                    <button type="submit"
                                        class="text-xs font-medium text-brand-600 hover:text-brand-500">OK</button>
                                </form>
                                <form method="POST" action="{{ route('pos.sale.detail.remove', $detail) }}"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-xs font-medium text-red-600 hover:text-red-500">Quitar</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-4 space-y-1 border-t border-gray-200 pt-3">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span>S/ {{ number_format($sale->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-gray-900">
                        <span>Total</span>
                        <span>S/ {{ number_format($sale->total, 2) }}</span>
                    </div>
                </div>

                <div class="mt-4 flex gap-3">
                    @if ($sale->details()->count() > 0)
                        <form method="POST" action="{{ route('pos.sale.kitchen', $sale) }}" class="flex-1">
                            @csrf
                            <button type="submit"
                                class="w-full rounded-md bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500">
                                Enviar a cocina
                            </button>
                        </form>
                        <a href="{{ route('pos.sale.precuenta', $sale) }}"
                            class="flex-1 rounded-md bg-slate-500 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-slate-400">
                            Precuenta
                        </a>
                        @can('pos-cobro')
                            <a href="{{ route('pos.checkout', $sale) }}"
                                class="flex-1 rounded-md bg-green-600 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                                Cobrar
                            </a>
                        @endcan
                    @else
                        <p class="text-sm text-gray-500">Agrega productos para comenzar el pedido.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const products = [...document.querySelectorAll('.pos-product')];
            const search = document.getElementById('pos-search');
            const categories = document.getElementById('pos-categories');

            let activeCat = 'all';

            const filter = () => {
                const term = search.value.trim().toLowerCase();
                products.forEach((p) => {
                    const byCat = activeCat === 'all' || p.dataset.cat === activeCat;
                    const byTerm = !term || p.dataset.name.toLowerCase().includes(term);
                    p.classList.toggle('hidden', !(byCat && byTerm));
                });
            };

            search.addEventListener('input', filter);

            categories.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-cat]');
                if (!btn) return;
                activeCat = btn.dataset.cat;
                categories.querySelectorAll('[data-cat]').forEach((b) => {
                    const active = b === btn;
                    b.className = active ?
                        'rounded-full bg-brand-500 px-3 py-1 text-xs font-semibold text-white' :
                        'rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-200';
                });
                filter();
            });

            document.querySelectorAll('.pos-detail-qty').forEach((row) => {
                const input = row.querySelector('input[name="quantity"]');
                const minus = row.querySelector('[data-qty-minus]');
                const plus = row.querySelector('[data-qty-plus]');

                const clamp = () => {
                    let v = parseInt(input.value, 10);
                    if (isNaN(v) || v < 1) v = 1;
                    input.value = v;
                };

                minus.addEventListener('click', () => {
                    input.value = Math.max(1, (parseInt(input.value, 10) || 1) - 1);
                });

                plus.addEventListener('click', () => {
                    input.value = (parseInt(input.value, 10) || 1) + 1;
                });

                input.addEventListener('change', clamp);
                input.addEventListener('blur', clamp);
            });

            // Carrito de selección: múltiples productos, cantidad y nota editables,
            // y un solo "Agregar al pedido" al final.
            const cart = new Map(); // productId -> { name, qty, notes }
            const cartBox = document.getElementById('pos-cart');
            const cartItems = document.getElementById('pos-cart-items');
            const cartCount = document.getElementById('pos-cart-count');
            const bulkForm = document.getElementById('pos-bulk-hidden');

            const clampQty = (value) => {
                let v = parseFloat(value);
                if (isNaN(v) || v <= 0) v = 1;
                return v;
            };

            const cartRow = (id, name) => {
                const row = document.createElement('li');
                row.className = 'pos-cart-item rounded-md border border-gray-200 bg-white p-3 shadow-sm';
                row.dataset.id = id;

                const top = document.createElement('div');
                top.className = 'flex items-center justify-between gap-2';

                const nameEl = document.createElement('p');
                nameEl.className = 'flex-1 truncate text-sm font-medium text-gray-900';
                nameEl.textContent = name;

                const stepper = document.createElement('div');
                stepper.className = 'flex items-center overflow-hidden rounded-md border border-gray-300 shadow-sm';

                const minus = document.createElement('button');
                minus.type = 'button';
                minus.dataset.cartMinus = '';
                minus.setAttribute('aria-label', 'Restar una unidad');
                minus.className = 'flex h-8 w-8 items-center justify-center text-gray-600 transition hover:bg-gray-100 hover:text-brand-600 active:bg-gray-200';
                minus.innerHTML = '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" /></svg>';

                const qty = document.createElement('input');
                qty.type = 'number';
                qty.value = cart.get(id).qty;
                qty.min = '0.5';
                qty.step = 'any';
                qty.className = 'pos-cart-qty h-8 w-12 border-x border-gray-300 text-center text-sm font-semibold text-gray-900 [-moz-appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none';

                const plus = document.createElement('button');
                plus.type = 'button';
                plus.dataset.cartPlus = '';
                plus.setAttribute('aria-label', 'Sumar una unidad');
                plus.className = 'flex h-8 w-8 items-center justify-center bg-brand-500 text-white transition hover:bg-brand-600 active:bg-brand-700';
                plus.innerHTML = '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>';

                stepper.appendChild(minus);
                stepper.appendChild(qty);
                stepper.appendChild(plus);

                const remove = document.createElement('button');
                remove.type = 'button';
                remove.dataset.cartRemove = '';
                remove.className = 'text-xs font-medium text-red-600 hover:text-red-500';
                remove.textContent = 'Quitar';

                top.appendChild(nameEl);
                top.appendChild(stepper);
                top.appendChild(remove);

                const notes = document.createElement('input');
                notes.type = 'text';
                notes.value = cart.get(id).notes;
                notes.placeholder = 'Nota (ej. 1 picante)';
                notes.className = 'pos-cart-notes mt-2 block w-full rounded-md border-gray-300 px-2 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500';

                row.appendChild(top);
                row.appendChild(notes);

                return row;
            };

            const renderCart = () => {
                cartItems.querySelectorAll('.pos-cart-item').forEach((row) => {
                    const item = cart.get(row.dataset.id);
                    if (!item) return;
                    item.qty = clampQty(row.querySelector('.pos-cart-qty').value);
                    item.notes = row.querySelector('.pos-cart-notes').value.trim();
                });

                cartItems.innerHTML = '';
                cart.forEach((item, id) => {
                    cartItems.appendChild(cartRow(id, item.name));
                });
                cartBox.classList.toggle('hidden', cart.size === 0);
                cartCount.textContent = cart.size === 1 ? '1 producto' : cart.size + ' productos';
                const submit = document.getElementById('pos-cart-submit');
                submit.disabled = cart.size === 0;
                submit.textContent = 'Agregar al pedido (' + cart.size + ')';
            };

            products.forEach((p) => {
                p.addEventListener('click', () => {
                    const id = p.dataset.product;
                    const name = p.dataset.name;
                    if (cart.has(id)) {
                        const row = cartItems.querySelector('.pos-cart-item[data-id="' + id + '"]');
                        const input = row && row.querySelector('.pos-cart-qty');
                        if (input) input.value = clampQty(input.value) + 1;
                    } else {
                        cart.set(id, { name: name, qty: 1, notes: '' });
                        renderCart();
                    }
                });
            });

            cartItems.addEventListener('click', (e) => {
                const row = e.target.closest('.pos-cart-item');
                if (!row) return;
                const id = row.dataset.id;
                const input = row.querySelector('.pos-cart-qty');

                if (e.target.closest('[data-cart-plus]')) {
                    input.value = clampQty(input.value) + 1;
                } else if (e.target.closest('[data-cart-minus]')) {
                    input.value = clampQty(input.value) - 1;
                } else if (e.target.closest('[data-cart-remove]')) {
                    cart.delete(id);
                    renderCart();
                }
            });

            cartItems.addEventListener('change', (e) => {
                if (e.target.classList.contains('pos-cart-qty')) {
                    e.target.value = clampQty(e.target.value);
                }
            });

            document.getElementById('pos-cart-clear').addEventListener('click', () => {
                cart.clear();
                renderCart();
            });

            document.getElementById('pos-cart-submit').addEventListener('click', () => {
                const items = [];
                cartItems.querySelectorAll('.pos-cart-item').forEach((row) => {
                    items.push({
                        product_id: row.dataset.id,
                        quantity: clampQty(row.querySelector('.pos-cart-qty').value),
                        notes: row.querySelector('.pos-cart-notes').value.trim(),
                    });
                });

                if (items.length === 0) return;

                bulkForm.querySelectorAll('input[name^="items"]').forEach((input) => input.remove());

                items.forEach((item, index) => {
                    const productInput = document.createElement('input');
                    productInput.type = 'hidden';
                    productInput.name = 'items[' + index + '][product_id]';
                    productInput.value = item.product_id;

                    const qtyInput = document.createElement('input');
                    qtyInput.type = 'hidden';
                    qtyInput.name = 'items[' + index + '][quantity]';
                    qtyInput.value = item.quantity;

                    const notesInput = document.createElement('input');
                    notesInput.type = 'hidden';
                    notesInput.name = 'items[' + index + '][notes]';
                    notesInput.value = item.notes;

                    bulkForm.appendChild(productInput);
                    bulkForm.appendChild(qtyInput);
                    bulkForm.appendChild(notesInput);
                });

                bulkForm.submit();
            });
        })();
    </script>
@endpush
