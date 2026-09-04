<?php

namespace Database\Factories\Sales;

use App\Models\Inventory\Product;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SaleDetail>
 */
class SaleDetailFactory extends Factory
{
    protected $model = SaleDetail::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomFloat(2, 5, 150);

        return [
            'sale_id' => Sale::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $quantity * $unitPrice,
            'notes' => null,
            'kitchen_status' => 'pending',
        ];
    }

    public function dish(): static
    {
        return $this->state(fn () => [
            'product_id' => Product::factory()->dish(),
        ]);
    }

    public function preparing(): static
    {
        return $this->state(fn () => [
            'kitchen_status' => 'preparing',
            'prep_started_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'kitchen_status' => 'completed',
            'prep_started_at' => now()->subMinutes(5),
            'prep_completed_at' => now(),
        ]);
    }
}
