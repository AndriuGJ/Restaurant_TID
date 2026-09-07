<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante {{ $sale->series }}-{{ $sale->number }}</title>
    <style>
        * {
            font-family: DejaVu Sans, sans-serif;
        }

        body {
            margin: 0;
            padding: 24px;
            color: #111827;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 16px;
            border-bottom: 2px solid #111827;
            padding-bottom: 12px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0 0 4px;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .doc {
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            margin: 14px 0 4px;
        }

        .doc .number {
            font-size: 16px;
        }

        .meta {
            margin: 8px 0;
        }

        .meta p {
            margin: 2px 0;
        }

        .divider {
            border-bottom: 1px solid #9ca3af;
            margin: 10px 0;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        table.items th {
            text-align: left;
            font-size: 11px;
            border-bottom: 1px solid #9ca3af;
            padding: 4px 6px;
        }

        table.items td {
            padding: 5px 6px;
            vertical-align: top;
        }

        table.items td.right,
        table.items th.right {
            text-align: right;
            white-space: nowrap;
        }

        table.totals {
            width: 100%;
            border-collapse: collapse;
        }

        table.totals td {
            padding: 3px 6px;
        }

        table.totals td.right {
            text-align: right;
            white-space: nowrap;
        }

        table.totals .total {
            font-size: 14px;
            font-weight: bold;
        }

        .payments {
            margin-top: 8px;
        }

        .payments table {
            width: 100%;
            border-collapse: collapse;
        }

        .payments td {
            padding: 3px 6px;
        }

        .payments td.right {
            text-align: right;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ mb_strtoupper($company->social_reason) }}</h1>
        @if ($company->commercial_name)
            <p>{{ $company->commercial_name }}</p>
        @endif
        @if ($company->ruc)
            <p>RUC: {{ $company->ruc }}</p>
        @endif
        @if ($company->fiscal_address)
            <p>{{ $company->fiscal_address }}</p>
        @endif
    </div>

    @php
        $isFactura = $sale->documentType?->nomenclature !== null
            && mb_strtoupper($sale->documentType->nomenclature) === 'F';
        $documentLabel = $isFactura ? 'FACTURA' : 'BOLETA';
    @endphp

    <div class="doc">
        {{ $documentLabel }} ELECTRÓNICA
        <div class="number">{{ $sale->series }}-{{ $sale->number }}</div>
    </div>

    <div class="meta">
        <p><b>Fecha:</b> {{ optional($sale->updated_at)->format('d/m/Y H:i') }}</p>
        <p><b>Pedido:</b> #{{ $sale->id }}</p>
        @if ($isFactura && $sale->clientable)
            <p><b>Cliente:</b> {{ $sale->clientable->social_reason }}</p>
            <p><b>RUC:</b> {{ $sale->clientable->ruc }}</p>
        @endif
    </div>

    <div class="divider"></div>

    <table class="items">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="right">Cantidad</th>
                <th class="right">P.U.</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->details as $detail)
                <tr>
                    <td>{{ $detail->product?->name }}</td>
                    <td class="right">{{ format_quantity($detail->quantity) }}</td>
                    <td class="right">S/ {{ number_format($detail->unit_price, 2) }}</td>
                    <td class="right">S/ {{ number_format($detail->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td class="right">S/ {{ number_format($sale->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>IGV (18%)</td>
            <td class="right">S/ {{ number_format($sale->subtotal * 0.18, 2) }}</td>
        </tr>
        <tr>
            <td class="total">TOTAL</td>
            <td class="right total">S/ {{ number_format($sale->total, 2) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="payments">
        <table>
            @foreach ($sale->payments as $payment)
                <tr>
                    <td>{{ mb_strtoupper((string) $payment->paymentMethod?->name) }}</td>
                    <td class="right">S/ {{ number_format($payment->amount, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td><b>Total pagado</b></td>
                <td class="right"><b>S/ {{ number_format($sale->payments->sum('amount'), 2) }}</b></td>
            </tr>
            @if ((float) $sale->change > 0)
                <tr>
                    <td><b>VUELTO</b></td>
                    <td class="right"><b>S/ {{ number_format($sale->change, 2) }}</b></td>
                </tr>
            @endif
        </table>
    </div>
</body>
</html>