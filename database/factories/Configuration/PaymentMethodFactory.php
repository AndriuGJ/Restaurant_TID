<?php

namespace Database\Factories\Configuration;

use App\Models\Configuration\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Efectivo',
                'Tarjeta de Débito',
                'Tarjeta de Crédito',
                'Yape',
                'Plin',
                'Visa',
                'Mastercard',
            ]),
            'type' => fake()->randomElement(['cash', 'card', 'digital']),
            'status' => true,
        ];
    }
}
