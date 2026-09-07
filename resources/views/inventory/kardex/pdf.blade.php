<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #000; }

        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .header-left { display: flex; align-items: center; gap: 12px; }
        .header-logo { width: 50px; height: 50px; }
        .header-info h1 { font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .header-info p { font-size: 10px; color: #444; margin-top: 2px; }
        .header-right { text-align: right; font-size: 9px; color: #444; }

        .filter-info { background: #f5f5f5; border: 1px solid #ddd; padding: 8px 12px; margin-bottom: 15px; font-size: 10px; }
        .filter-info strong { font-weight: bold; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table th, table td { border: 1px solid #000; padding: 6px 8px; text-align: left; font-size: 9px; }
        table th { background: #000; color: #fff; font-weight: bold; text-transform: uppercase; font-size: 8px; letter-spacing: 0.5px; }
        table td { vertical-align: top; }
        table tr:nth-child(even) td { background: #f9f9f9; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        .footer { border-top: 2px solid #000; padding-top: 8px; font-size: 8px; color: #666; text-align: center; }

        .badge { display: inline-block; padding: 2px 6px; font-size: 8px; font-weight: bold; border: 1px solid #000; }
        .badge-purchase { background: #000; color: #fff; }
        .badge-sale { background: #fff; color: #000; }
        .badge-adjustment { background: #888; color: #fff; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @if ($logoPath)
                <img src="{{ $logoPath }}" class="header-logo" alt="Logo">
            @endif
            <div class="header-info">
                <h1>{{ $company?->commercial_name ?? $company?->name ?? 'Restaurante' }}</h1>
                <p>RUC: {{ $company?->ruc ?? '—' }}</p>
            </div>
        </div>
        <div class="header-right">
            <p><strong>{{ $title }}</strong></p>
            <p>Generado: {{ $generatedAt }}</p>
        </div>
    </div>

    <div class="filter-info">
        <strong>Filtro:</strong> {{ $productName ?? 'Todos los productos' }} ·
        <strong>Movimientos:</strong> {{ $movements->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%">Fecha</th>
                <th style="width: 22%">Producto</th>
                <th style="width: 13%">Movimiento</th>
                <th style="width: 10%" class="text-right">Entrada</th>
                <th style="width: 10%" class="text-right">Salida</th>
                <th style="width: 10%" class="text-right">Saldo</th>
                <th style="width: 23%">Documento</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movements as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                    <td class="font-bold">{{ $movement->product?->name }}</td>
                    <td>
                        @php
                            $badgeClass = match($movement->movement_type) {
                                'purchase' => 'badge-purchase',
                                'sale' => 'badge-sale',
                                default => 'badge-adjustment',
                            };
                            $typeLabel = match($movement->movement_type) {
                                'purchase' => 'COMPRA',
                                'sale' => 'VENTA',
                                default => ucfirst($movement->movement_type),
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $typeLabel }}</span>
                    </td>
                    <td class="text-right">{{ $movement->quantity_in > 0 ? number_format($movement->quantity_in, 2) : '—' }}</td>
                    <td class="text-right">{{ $movement->quantity_out > 0 ? number_format($movement->quantity_out, 2) : '—' }}</td>
                    <td class="text-right font-bold">{{ number_format($movement->balance, 2) }}</td>
                    <td>{{ $movement->relatedDocument ? class_basename($movement->related_document_type).' #'.$movement->related_document_id : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No hay movimientos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        {{ $company?->name ?? 'Restaurante' }} · {{ $company?->commercial_address ?? '' }} · RUC: {{ $company?->ruc ?? '—' }}
    </div>
</body>
</html>
