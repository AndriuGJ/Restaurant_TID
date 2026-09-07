<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'dejavusans', Helvetica, Arial, sans-serif; font-size: 10px; color: #000; }

        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .header-left { display: flex; align-items: center; gap: 12px; }
        .header-logo { width: 50px; height: 50px; }
        .header-info h1 { font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .header-info p { font-size: 10px; color: #444; margin-top: 2px; }
        .header-right { text-align: right; font-size: 9px; color: #444; }

        .filter-info { background: #f5f5f5; border: 1px solid #ddd; padding: 8px 12px; margin-bottom: 15px; font-size: 10px; }
        .filter-info strong { font-weight: bold; }

        .summary { margin-bottom: 15px; font-size: 10px; }
        .summary table { width: 100%; border-collapse: collapse; }
        .summary td { padding: 6px 8px; border: 1px solid #000; }
        .summary .metric { font-size: 9px; color: #444; text-transform: uppercase; }
        .summary .value { font-size: 14px; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table th, table td { border: 1px solid #000; padding: 6px 8px; text-align: left; font-size: 9px; }
        table th { background: #000; color: #fff; font-weight: bold; text-transform: uppercase; font-size: 8px; letter-spacing: 0.5px; }
        table td { vertical-align: top; }
        table tr:nth-child(even) td { background: #f9f9f9; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        .footer { border-top: 2px solid #000; padding-top: 8px; font-size: 8px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @if ($logoDataUri)
                <img src="{{ $logoDataUri }}" class="header-logo" alt="Logo">
            @endif
            <div class="header-info">
                <h1>{{ $company?->commercial_name ?? $company?->name ?? 'Restaurante' }}</h1>
                @if ($company?->ruc)
                    <p>RUC: {{ $company->ruc }}</p>
                @endif
            </div>
        </div>
        <div class="header-right">
            <p><strong>{{ $title }}</strong></p>
            <p>Generado: {{ $generatedAt }}</p>
        </div>
    </div>

    <div class="filter-info">
        <strong>Filtro:</strong>
        {{ $from || $to ? 'del '.($from ?? '—').' al '.($to ?? '—') : 'sin rango de fechas' }}
        @if ($saleType)
            · <strong>Tipo:</strong> {{ $saleTypeLabel }}
        @endif
        @if ($documentTypeName)
            · <strong>Comprobante:</strong> {{ $documentTypeName }}
        @endif
        · <strong>Ventas:</strong> {{ $sales->count() }}
    </div>

    <div class="summary">
        <table>
            <tr>
                <td class="text-center">
                    <div class="metric">Total ventas</div>
                    <div class="value">{{ number_format($summary->total_count, 0) }}</div>
                </td>
                <td class="text-center">
                    <div class="metric">Monto recaudado</div>
                    <div class="value">S/ {{ number_format($summary->total_amount, 2) }}</div>
                </td>
                <td class="text-center">
                    <div class="metric">Ticket promedio</div>
                    <div class="value">S/ {{ $summary->total_count ? number_format($summary->total_amount / $summary->total_count, 2) : '0.00' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 15%">Fecha</th>
                <th style="width: 12%">Tipo</th>
                <th style="width: 28%">Cliente</th>
                <th style="width: 20%">Comprobante</th>
                <th style="width: 20%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td>#{{ $sale->id }}</td>
                    <td>{{ $sale->updated_at?->format('d/m/Y H:i') }}</td>
                    <td>
                        @php
                            $typeLabels = ['pos' => 'Salón', 'delivery' => 'Delivery', 'quick_sale' => 'Venta rápida'];
                        @endphp
                        {{ $typeLabels[$sale->sale_type] ?? ucfirst((string) $sale->sale_type) }}
                    </td>
                    <td>{{ $sale->clientable?->social_reason ?: $sale->clientable?->name ?: 'Público general' }}</td>
                    <td>
                        @if ($sale->series && $sale->number)
                            {{ $sale->series }}-{{ $sale->number }}
                        @else
                            {{ $sale->documentType?->name ?? '—' }}
                        @endif
                    </td>
                    <td class="text-right font-bold">S/ {{ number_format($sale->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No hay ventas para los filtros indicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        {{ $company?->name ?? 'Restaurante' }} · {{ $company?->commercial_address ?? '' }} · RUC: {{ $company?->ruc ?? '—' }}
    </div>
</body>
</html>