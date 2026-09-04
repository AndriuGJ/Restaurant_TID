<?php

namespace Database\Factories\Sales;

use App\Models\Configuration\PaymentMethod;
use App\Models\Sales\Sale;
use App\Models\Sales\SalePayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalePayment>
 */
class SalePaymentFactory extends Factory
{
    protected $model = SalePayment::class;

    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'amount' => fake()->randomFloat(2, 5, 300),
        ];
    }
}
