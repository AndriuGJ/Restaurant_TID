<?php

namespace Database\Seeders;

use App\Models\Configuration\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $paymentMethods = [
            ['name' => 'Efectivo', 'type' => 'cash'],
            ['name' => 'Tarjeta', 'type' => 'card'],
            ['name' => 'Yape', 'type' => 'digital'],
            ['name' => 'Plin', 'type' => 'digital'],
        ];

        foreach ($paymentMethods as $paymentMethod) {
            PaymentMethod::firstOrCreate(
                ['name' => $paymentMethod['name']],
                $paymentMethod
            );
        }
    }
}
