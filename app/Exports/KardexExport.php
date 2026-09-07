<?php

namespace App\Exports;

use App\Models\Kardex\KardexMovement;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KardexExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @param  Collection<int, KardexMovement>  $movements
     */
    public function __construct(
        private readonly Collection $movements,
        private readonly ?string $productName,
    ) {}

    /**
     * @return Collection<int, KardexMovement>
     */
    public function collection(): Collection
    {
        return $this->movements;
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return ['Fecha', 'Producto', 'Tipo de movimiento', 'Entrada', 'Salida', 'Saldo', 'Documento'];
    }

    /**
     * @param  KardexMovement  $movement
     * @return array<int, string|int|float|null>
     */
    public function map(mixed $movement): array
    {
        $typeLabels = [
            'purchase' => 'Compra',
            'sale' => 'Venta',
            'adjustment' => 'Ajuste',
        ];

        return [
            $movement->created_at->format('d/m/Y H:i'),
            $movement->product?->name,
            $typeLabels[$movement->movement_type] ?? ucfirst($movement->movement_type),
            $movement->quantity_in > 0 ? $movement->quantity_in : null,
            $movement->quantity_out > 0 ? $movement->quantity_out : null,
            $movement->balance,
            $movement->relatedDocument
                ? class_basename($movement->related_document_type).' #'.$movement->related_document_id
                : null,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
