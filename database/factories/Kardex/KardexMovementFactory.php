<?php

namespace Database\Factories\Kardex;

use App\Models\Inventory\Product;
use App\Models\Kardex\KardexMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KardexMovement>
 */
class KardexMovementFactory extends Factory
{
    protected $model = KardexMovement::class;

    public function definition(): array
    {
        $quantityIn = fake()->randomFloat(2, 1, 200);

        return [
            'product_id' => Product::factory()->supply(),
            'movement_type' => 'purchase',
            'quantity_in' => $quantityIn,
            'quantity_out' => 0,
            'balance' => $quantityIn,
            'related_document_type' => null,
            'related_document_id' => null,
        ];
    }
}
