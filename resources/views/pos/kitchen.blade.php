@extends('layouts.app')

@section('title', 'Cocina')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cocina</h1>
            <p class="mt-1 text-sm text-gray-600">Pedidos pendientes de preparación</p>
        </div>
        <a href="{{ route('pos.hall') }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500">← Volver al salón</a>
    </div>

    @if ($orders->isEmpty())
        <div class="mt-8 rounded-md border border-dashed border-gray-300 bg-white p-10 text-center">
            <p class="text-sm text-gray-500">No hay pedidos pendientes en cocina.</p>
        </div>
    @else
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($orders as $group)
                @php
                    $first = $group->first();
                    $sale = $first?->sale;
                    $table = $sale?->table;
                @endphp
                <div class="rounded-md border border-orange-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">
                            Pedido #{{ $sale->id }}
                        </span>
                        @if ($sale->sale_type === 'delivery')
                            <span class="rounded-full bg-teal-100 px-2 py-0.5 text-[10px] font-bold text-teal-700">DELIVERY</span>
                        @elseif ($sale->sale_type === 'quick_sale')
                            <span class="rounded-full bg-fuchsia-100 px-2 py-0.5 text-[10px] font-bold text-fuchsia-700">PARA LLEVAR</span>
                        @endif
                    </div>

                    @if ($table)
                        <p class="mt-2 text-sm font-semibold text-gray-900">Mesa {{ $table->name }}</p>
                    @elseif ($sale->sale_type === 'delivery')
                        <p class="mt-2 text-sm font-semibold text-gray-900">
                            {{ $sale->deliveryProvider?->name ?? 'Personal propio' }}
                            @if ($sale->delivery_person_name)
                                · {{ $sale->delivery_person_name }}
                            @endif
                        </p>
                    @else
                        <p class="mt-2 text-sm font-semibold text-gray-900">Para llevar</p>
                    @endif

                    <ul class="mt-3 space-y-2">
                        @foreach ($group as $detail)
                            <li class="flex items-center justify-between gap-2 border-b border-gray-100 py-1 last:border-0">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-gray-900">
                                        {{ format_quantity($detail->quantity) }} × {{ $detail->product?->name }}
                                    </p>
                                    @if ($detail->notes)
                                        <p class="truncate text-xs text-gray-500">"{{ $detail->notes }}"</p>
                                    @endif
                                </div>
                                <span class="whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $detail->kitchen_status === 'preparing' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $detail->kitchen_status === 'preparing' ? 'EN PREPARACIÓN' : 'PENDIENTE' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-3 flex gap-2">
                        @if ($group->contains(fn ($d) => $d->kitchen_status === 'pending'))
                            <form method="POST" action="{{ route('pos.kitchen.start', $group->firstWhere('kitchen_status', 'pending')) }}" class="flex-1">
                                @csrf
                                <button type="submit"
                                    class="w-full rounded-md bg-brand-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                                    Iniciar
                                </button>
                            </form>
                        @endif
                        @foreach ($group->where('kitchen_status', 'preparing') as $detail)
                            <form method="POST" action="{{ route('pos.kitchen.complete', $detail) }}" class="flex-1">
                                @csrf
                                <button type="submit"
                                    class="w-full rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                                    Completar
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection