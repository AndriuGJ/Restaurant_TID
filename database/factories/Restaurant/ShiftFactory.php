<?php

namespace Database\Factories\Restaurant;

use App\Models\Restaurant\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shift>
 */
class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Mañana',
                'Tarde',
                'Noche',
                'Madrugada',
                'Fin de semana',
            ]),
            'status' => true,
        ];
    }
}
