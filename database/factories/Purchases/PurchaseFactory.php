<?php

namespace Database\Factories\Purchases;

use App\Models\Configuration\DocumentType;
use App\Models\Inventory\Supplier;
use App\Models\Purchases\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'user_id' => User::factory(),
            'purchase_type' => fake()->randomElement(['contado', 'credito']),
            'document_type_id' => DocumentType::factory()->invoice(),
            'series' => fake()->optional()->randomElement(['F001', 'B001']),
            'number' => fake()->unique()->numerify('#####'),
            'purchase_date' => fake()->date(),
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
            'status' => 'completed',
        ];
    }
}
