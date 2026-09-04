<?php

namespace Database\Factories\Restaurant;

use App\Models\Restaurant\Hall;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hall>
 */
class HallFactory extends Factory
{
    protected $model = Hall::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Salón Principal',
                'Terraza',
                'VIP',
                'Salón Norte',
                'Cava',
            ]),
            'status' => true,
        ];
    }
}
