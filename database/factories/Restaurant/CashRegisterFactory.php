<?php

namespace Database\Factories\Restaurant;

use App\Models\Restaurant\CashRegister;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashRegister>
 */
class CashRegisterFactory extends Factory
{
    protected $model = CashRegister::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Caja Principal',
                'Caja 01',
                'Caja 02',
                'Caja Barra',
                'Caja Delivery',
            ]),
            'status' => true,
        ];
    }
}
