<?php

namespace App\Services;

use App\Models\Configuration\Company;
use Illuminate\Contracts\View\Factory;

class TcpdfService
{
    public function __construct(
        private readonly Factory $view,
    ) {
        if (! defined('K_PATH_FONTS')) {
            define('K_PATH_FONTS', base_path('vendor/tecnickcom/tc-lib-pdf-font/target/fonts/'));
        }
    }

    /**
     * Render a Blade view to PDF bytes using TCPDF.
     *
     * @param  array<string, mixed>  $data
     */
    public function render(string $view, array $data = [], array $paper = ['orientation' => 'P', 'format' => 'A4']): string
    {
        $html = $this->view->make($view, $data)->render();

        $pdf = new \TCPDF(
            $paper['orientation'] ?? 'P',
            'mm',
            $paper['format'] ?? 'A4',
            true,
            'UTF-8'
        );

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(12, 12, 12);
        $pdf->SetAutoPageBreak(true, 12);
        $pdf->SetCreator('Sistema Restaurante');
        $pdf->SetFont('dejavusans', '', 9);

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('output.pdf', 'S');
    }

    /**
     * Build a data-URI logo from the configured company for use inside <img> tags.
     */
    public function logoDataUri(?Company $company): ?string
    {
        if (! $company || ! $company->logo) {
            return null;
        }

        $fullPath = storage_path('app/public/'.$company->logo);
        if (! is_file($fullPath)) {
            return null;
        }

        $mime = (string) (mime_content_type($fullPath) ?: 'image/png');

        return "data:{$mime};base64,".base64_encode((string) file_get_contents($fullPath));
    }
}
