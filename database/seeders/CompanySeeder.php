<?php

namespace Database\Seeders;

use App\Models\Configuration\Company;
use App\Models\Configuration\Ubigeo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $ubicacion = Ubigeo::where('code', '150101')->first();

        Company::firstOrCreate(
            ['ruc' => '20123456789'],
            [
                'name' => 'Restaurante Demo S.A.C.',
                'commercial_name' => 'Restaurante Demo',
                'phone' => '(01) 555-1234',
                'commercial_address' => 'Av. Larco 123, Miraflores',
                'ruc' => '20123456789',
                'social_reason' => 'Restaurante Demo S.A.C.',
                'fiscal_address' => 'Av. Larco 123, Miraflores, Lima',
                'ubigeo_id' => $ubicacion?->id,
                // Datos SOL (SOL - SUNAT Operaciones en Línea) — reemplazar por los reales
                'sol_user' => 'DEMO12345678',
                'sol_password' => 'demo-sol-password',
                'digital_certificate_path' => null,
            ]
        );
    }
}
