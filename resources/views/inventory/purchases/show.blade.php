@extends('layouts.app')

@section('title', 'Compra')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Compra</h1>
        <a href="{{ route('inventory.purchases.index') }}"
            class="text-sm font-medium text-brand-600 hover:text-brand-500">Volver</a>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Proveedor</p>
            <p class="mt-1 text-sm text-gray-900">{{ $purchase->supplier?->social_reason }}</p>
            <p class="mt-2 text-xs text-gray-500">{{ $purchase->supplier?->ruc }}</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Comprobante</p>
            <p class="mt-1 text-sm text-gray-900">
                {{ $purchase->documentType?->name }} {{ $purchase->series }}-{{ $purchase->number }}
            </p>
            <p class="mt-2 text-xs text-gray-500">
                Fecha: {{ $purchase->purchase_date->format('d/m/Y') }} · {{ ucfirst($purchase->purchase_type) }}
            </p>
        </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Producto</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Cantidad</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">P. unitario</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($purchase->details as $detail)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $detail->product?->name }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600">{{ number_format($detail->quantity, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600">S/ {{ number_format($detail->unit_price, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600">S/ {{ number_format($detail->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-700">Subtotal</td>
                    <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">S/ {{ number_format($purchase->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-700">Total</td>
                    <td class="px-6 py-3 text-right text-sm font-semibold text-brand-600">S/ {{ number_format($purchase->total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
