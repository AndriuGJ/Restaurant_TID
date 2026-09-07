<?php

namespace App\Services;

use App\Exports\KardexExport;
use App\Models\Configuration\Company;
use App\Models\Kardex\KardexMovement;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class KardexExportService
{
    public function __construct(
        private readonly TcpdfService $tcpdf,
    ) {}

    /**
     * @param  Collection<int, KardexMovement>  $movements
     */
    public function exportPdf(Collection $movements, ?string $productName): Response
    {
        $company = Company::first();
        $title = $productName ? "Kardex — {$productName}" : 'Kardex — Todos los productos';

        $pdf = $this->tcpdf->render('inventory.kardex.pdf', [
            'movements' => $movements,
            'company' => $company,
            'title' => $title,
            'logoPath' => $this->tcpdf->logoDataUri($company),
            'generatedAt' => now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y, h:i a'),
        ], ['orientation' => 'L']);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @param  Collection<int, KardexMovement>  $movements
     */
    public function exportExcel(Collection $movements, ?string $productName): BinaryFileResponse
    {
        return Excel::download(
            new KardexExport($movements, $productName),
            'kardex_'.now()->format('Y-m-d_His').'.xlsx'
        );
    }
}
