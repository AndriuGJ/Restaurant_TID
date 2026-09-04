<?php

namespace Database\Factories\Restaurant;

use App\Models\Restaurant\Hall;
use App\Models\Restaurant\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Table>
 */
class TableFactory extends Factory
{
    protected $model = Table::class;

    public function definition(): array
    {
        return [
            'hall_id' => Hall::factory(),
            'name' => fake()->unique()->numerify('Mesa ##'),
            'shape' => fake()->randomElement(['square', 'round', 'rectangular']),
            'status' => 'available',
        ];
    }
}
