@extends('layouts.app')

@section('title', 'Comprobante {{ $sale->series }}-{{ $sale->number }}')

@section('content')
    @php
        $isFactura = $sale->documentType?->nomenclature !== null
            && mb_strtoupper($sale->documentType->nomenclature) === 'F';
        $documentLabel = $isFactura ? 'FACTURA' : 'BOLETA';
        $payout = $sale->payments->sum('amount');
    @endphp

    <div class="mx-auto mt-6 max-w-sm">
        {{-- Ticket 80 mm --}}
        <div id="ticket" class="overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
            <div class="p-6 font-mono text-[13px] leading-relaxed text-gray-900">
                {{-- Encabezado de la empresa --}}
                <div class="text-center">
                    @if ($company->logo)
                        <img src="{{ asset('storage/'.$company->logo) }}" alt="{{ $company->commercial_name }}"
                            class="mx-auto mb-1 h-12 w-12 object-contain">
                    @endif
                    <p class="text-sm font-bold uppercase tracking-wide">{{ $company->social_reason }}</p>
                    @if ($company->commercial_name)
                        <p class="text-xs text-gray-600">{{ $company->commercial_name }}</p>
                    @endif
                    <p class="text-xs mt-0.5">RUC: {{ $company->ruc }}</p>
                    <p class="text-xs text-gray-600">{{ $company->fiscal_address }}</p>
                </div>

                <div class="my-2 border-t border-dashed border-gray-300"></div>

                {{-- Datos del comprobante --}}
                <div class="text-center">
                    <p class="text-sm font-bold">{{ $documentLabel }} ELECTRÓNICA</p>
                    <p class="text-lg font-bold tracking-wider">{{ $sale->series }}-{{ $sale->number }}</p>
                </div>

                <p class="mt-1">Fecha: {{ \Carbon\Carbon::parse($sale->updated_at)->format('d/m/Y H:i') }}</p>
                <p>Pedido: #{{ $sale->id }}</p>

                @if ($isFactura && $sale->clientable)
                    <div class="mt-1 border-t border-dashed border-gray-300 pt-1">
                        <p><span class="font-bold">Cliente:</span> {{ $sale->clientable->social_reason }}</p>
                        <p><span class="font-bold">RUC:</span> {{ $sale->clientable->ruc }}</p>
                    </div>
                @endif

                <div class="my-2 border-t border-dashed border-gray-300"></div>

                {{-- Detalle --}}
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

                {{-- Totales --}}
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

                {{-- Pagos y vuelto --}}
                @foreach ($sale->payments as $payment)
                    <div class="flex justify-between">
                        <span class="uppercase">{{ $payment->paymentMethod?->name }}</span>
                        <span>S/ {{ number_format($payment->amount, 2) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between">
                    <span>Total pagado</span>
                    <span>S/ {{ number_format($payout, 2) }}</span>
                </div>
                @if ((float) $sale->change > 0)
                    <div class="flex justify-between text-base font-bold">
                        <span>VUELTO</span>
                        <span>S/ {{ number_format($sale->change, 2) }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Acciones --}}
        <div class="mt-4 flex flex-wrap gap-2 print:hidden">
            <form method="POST" action="{{ route('pos.sale.receipt.print', $sale) }}" class="flex-1">
                @csrf
                <button type="submit"
                    class="w-full rounded-md bg-brand-500 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                    Imprimir en red
                </button>
            </form>
            <button type="button" onclick="window.print()"
                class="flex-1 rounded-md border border-gray-300 px-4 py-2 text-center text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                Imprimir aquí (PC)
            </button>
            @if ($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank"
                    class="flex-1 rounded-md bg-green-600 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                    Enviar por WhatsApp
                </a>
            @endif
            <a href="{{ route('pos.hall') }}"
                class="w-full rounded-md border border-gray-300 px-4 py-2 text-center text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                Volver al punto de venta
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
        #ticket,
        #ticket * {
            visibility: visible;
        }
        #ticket {
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