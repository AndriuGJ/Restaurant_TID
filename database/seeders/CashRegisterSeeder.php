<?php

namespace Database\Seeders;

use App\Models\Restaurant\CashRegister;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CashRegisterSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $cashRegisters = [
            ['name' => 'Caja Principal', 'status' => true],
            ['name' => 'Caja Delivery', 'status' => true],
        ];

        foreach ($cashRegisters as $cashRegister) {
            CashRegister::firstOrCreate(
                ['name' => $cashRegister['name']],
                $cashRegister
            );
        }
    }
}
