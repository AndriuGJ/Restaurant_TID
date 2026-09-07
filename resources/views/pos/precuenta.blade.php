@extends('layouts.app')

@section('title', "Precuenta — Pedido #{$sale->id}")

@section('content')
    <div class="mx-auto mt-6 max-w-sm">
        <div id="precuenta" class="overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
            <div class="p-6 font-mono text-[13px] leading-relaxed text-gray-900">
                <div class="text-center">
                    <p class="text-2xl font-black tracking-widest">PRECUENTA</p>
                    <p class="mt-1 text-xs text-gray-500">SOLO RESUMEN · NO ES LA CUENTA OFICIAL</p>
                </div>

                <div class="my-2 border-t border-dashed border-gray-300"></div>

                <p>
                    @if ($sale->sale_type === 'delivery')
                        Delivery
                    @elseif ($sale->sale_type === 'quick_sale')
                        Para llevar
                    @else
                        Mesa: <span class="font-bold">{{ $sale->table?->name ?? '—' }}</span>
                    @endif
                </p>
                <p>Pedido: #{{ $sale->id }} · {{ $sale->details_count }} producto(s)</p>
                <p>Fecha: {{ \Carbon\Carbon::parse($sale->updated_at)->format('d/m/Y H:i') }}</p>

                <div class="my-2 border-t border-dashed border-gray-300"></div>

                @foreach ($sale->details as $detail)
                    <div class="flex justify-between gap-2">
                        <span class="truncate">{{ $detail->product?->name }}</span>
                        <span class="whitespace-nowrap">{{ format_quantity($detail->quantity) }} x {{ number_format($detail->unit_price, 2) }}</span>
                    </div>
                    <div class="flex justify-end gap-2">
                        <span>S/ {{ number_format($detail->subtotal, 2) }}</span>
                    </div>
                @endforeach

                <div class="my-2 border-t border-dashed border-gray-300"></div>

                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>S/ {{ number_format($sale->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>IGV (18%)</span>
                    <span>S/ {{ number_format($sale->subtotal * 0.18, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-bold">
                    <span>TOTAL</span>
                    <span>S/ {{ number_format($sale->total, 2) }}</span>
                </div>

                <div class="my-2 border-t border-dashed border-gray-300"></div>

                <p class="text-center text-xs text-gray-500">Gracias por su visita.</p>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2 print:hidden">
            <button type="button" onclick="window.print()"
                class="flex-1 rounded-md bg-brand-500 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                Imprimir aquí (PC)
            </button>
            <a href="{{ route('pos.sale', $sale) }}"
                class="w-full rounded-md border border-gray-300 px-4 py-2 text-center text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                Volver al pedido
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('keydown', function (event) {
            if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'p') {
                window.print();
            }
        });
    </script>
@endpush

<style>
    @@media print {
        @@page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            margin: 0;
        }
        body * {
            visibility: hidden;
        }
        #precuenta,
        #precuenta * {
            visibility: visible;
        }
        #precuenta {
            position: absolute;
            left: 0;
            top: 0;
            width: 80mm;
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }
    }
</style>