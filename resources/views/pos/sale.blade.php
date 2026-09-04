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
                    <span class="rounded-full bg-fuchsia-100 px-2 py-0.5 text-xs font-semibold text-fuchsia-700">PARA LLEVAR</span>
                @else
                    Mesa <span class="font-semibold">{{ $sale->table?->name ?? '—' }}</span>
                @endif
                · {{ $sale->details_count }} productos
                · Estado: {{ strtoupper($sale->status) }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pos.hall') }}"
                class="text-sm font-medium text-gray-600 hover:text-gray-500">← Volver al salón</a>
            <form method="POST" action="{{ route('pos.sale.cancel', $sale) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="text-sm font-medium text-red-600 hover:text-red-500"
                    onclick="return confirm('¿Cancelar esta venta?')">Cancelar</button>
            </form>
        </div>
    </div>

    @if ($sale->sale_type === 'delivery')
        <div class="mt-6 rounded-md border border-teal-200 bg-white p-4 shadow-sm">
            <form method="POST" action="{{ route('pos.sale.delivery', $sale) }}" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
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
                    <input type="text" name="delivery_person_name" value="{{ old('delivery_person_name', $sale->delivery_person_name) }}"
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

                <div class="grid max-h-[480px] grid-cols-2 gap-3 overflow-y-auto sm:grid-cols-3"
                    id="pos-products">
                    @foreach ($products as $product)
                        <button type="button" data-product="{{ $product->id }}" data-cat="{{ $product->product_category_id ?? '' }}" data-name="{{ $product->name }}"
                            class="pos-product rounded-md border border-gray-200 p-2 text-left shadow-sm hover:border-brand-400 hover:shadow">
                            @if ($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy"
                                    class="mb-2 h-24 w-full rounded-md object-cover">
                            @else
                                <div class="mb-2 flex h-24 w-full items-center justify-center rounded-md bg-gray-100 text-gray-400">
                                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 6h16M4 6a2 2 0 00-2 2v8a2 2 0 002 2v0a2 2 0 002-2" />
                                    </svg>
                                </div>
                            @endif
                            <p class="truncate text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                            <p class="mt-1 text-sm font-bold text-brand-600">S/ {{ number_format($product->sale_price, 2) }}</p>
                        </button>
                    @endforeach
                </div>
                @error('product_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('quantity')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Detalle del pedido --}}
        <div class="lg:col-span-2">
            <div class="rounded-md border border-gray-200 bg-white p-4 shadow-sm">
                <div id="pos-add-form" class="mb-4 hidden rounded-md bg-gray-50 p-3">
                    <p id="pos-selected-name" class="text-sm font-semibold text-gray-900"></p>
                    <div class="mt-2 flex items-center gap-2">
                        <input id="pos-quantity" type="number" value="1" min="1" step="1"
                            class="w-24 rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        <input id="pos-notes" type="text" placeholder="Nota (ej. 1 picante)"
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <button id="pos-add-submit"
                        class="mt-2 rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                        Agregar al pedido
                    </button>
                    <form id="pos-add-hidden" method="POST" action="{{ route('pos.sale.add-product', $sale) }}" class="hidden">
                        @csrf
                        <input type="hidden" name="product_id" id="pos-product-id">
                        <input type="hidden" name="quantity" id="pos-quantity-hidden">
                        <input type="hidden" name="notes" id="pos-notes-hidden">
                    </form>
                </div>

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
                                    {{ $detail->quantity }} × S/ {{ number_format($detail->unit_price, 2) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <form method="POST" action="{{ route('pos.sale.detail.update', $detail) }}" class="flex items-center gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $detail->quantity }}" min="1" step="1"
                                        class="w-16 rounded-md border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <button type="submit" class="text-xs font-medium text-brand-600 hover:text-brand-500">OK</button>
                                </form>
                                <form method="POST" action="{{ route('pos.sale.detail.remove', $detail) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-500">Quitar</button>
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
            const addForm = document.getElementById('pos-add-form');
            const selectedName = document.getElementById('pos-selected-name');
            const productId = document.getElementById('pos-product-id');
            const quantity = document.getElementById('pos-quantity');
            const notes = document.getElementById('pos-notes');

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
                    b.className = active
                        ? 'rounded-full bg-brand-500 px-3 py-1 text-xs font-semibold text-white'
                        : 'rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-200';
                });
                filter();
            });

            products.forEach((p) => {
                p.addEventListener('click', () => {
                    productId.value = p.dataset.product;
                    selectedName.textContent = p.dataset.name;
                    quantity.value = 1;
                    notes.value = '';
                    addForm.classList.remove('hidden');
                    addForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });
            });

            document.getElementById('pos-add-submit').addEventListener('click', () => {
                document.getElementById('pos-quantity-hidden').value = quantity.value;
                document.getElementById('pos-notes-hidden').value = notes.value;
                document.getElementById('pos-add-hidden').submit();
            });
        })();
    </script>
@endpush