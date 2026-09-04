<?php

namespace Database\Factories\Sales;

use App\Models\Restaurant\Table;
use App\Models\Sales\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        $total = fake()->randomFloat(2, 20, 500);

        return [
            'user_id' => User::factory(),
            'table_id' => Table::factory(),
            'guests' => fake()->numberBetween(1, 8),
            'sale_type' => 'pos',
            'is_takeaway' => false,
            'subtotal' => $total,
            'tax' => 0,
            'total' => $total,
            'status' => 'pending',
        ];
    }

    public function atTable(Table $table): static
    {
        return $this->state(fn () => ['table_id' => $table->id]);
    }

    public function paid(): static
    {
        return $this->state(fn () => ['status' => 'paid']);
    }
}
