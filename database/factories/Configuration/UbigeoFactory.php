<?php

namespace Database\Factories\Configuration;

use App\Models\Configuration\Ubigeo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ubigeo>
 */
class UbigeoFactory extends Factory
{
    protected $model = Ubigeo::class;

    public function definition(): array
    {
        return [
            'department' => fake()->randomElement(['LIMA', 'AREQUIPA', 'CUSCO', 'LA LIBERTAD']),
            'province' => fake()->randomElement(['LIMA', 'AREQUIPA', 'CUSCO', 'TRUJILLO']),
            'district' => fake()->city(),
            'code' => fake()->unique()->numerify('######'),
        ];
    }
}
