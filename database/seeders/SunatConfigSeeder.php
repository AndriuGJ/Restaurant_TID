<?php

namespace Database\Seeders;

use App\Models\Configuration\Company;
use App\Models\Configuration\SunatConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SunatConfigSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $company = Company::first();

        if ($company === null) {
            return;
        }

        // Configuración vigente: 1 año desde el inicio del año actual
        $start = now()->startOfYear();
        $end = now()->endOfYear();

        SunatConfig::firstOrCreate(
            ['company_id' => $company->id],
            [
                'company_id' => $company->id,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'status' => 'active',
                'max_receipts' => 10000,
                'used_receipts' => 0,
                'card_surcharge_percentage' => 0.00,
            ]
        );
    }
}
