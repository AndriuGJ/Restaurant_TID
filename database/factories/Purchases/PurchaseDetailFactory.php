<?php

namespace Database\Factories\Purchases;

use App\Models\Inventory\Product;
use App\Models\Purchases\Purchase;
use App\Models\Purchases\PurchaseDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseDetail>
 */
class PurchaseDetailFactory extends Factory
{
    protected $model = PurchaseDetail::class;

    public function definition(): array
    {
        $quantity = fake()->randomFloat(2, 1, 100);
        $unitPrice = fake()->randomFloat(2, 1, 80);

        return [
            'purchase_id' => Purchase::factory(),
            'product_id' => Product::factory()->supply(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $quantity * $unitPrice,
        ];
    }
}
