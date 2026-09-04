<?php

namespace Database\Seeders;

use App\Models\Configuration\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $currencies = [
            ['name' => 'Sol Peruano', 'symbol' => 'S/', 'is_default' => true],
            ['name' => 'Dólar Americano', 'symbol' => '$', 'is_default' => false],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(
                ['name' => $currency['name']],
                $currency
            );
        }
    }
}
