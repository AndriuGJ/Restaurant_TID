<?php

namespace Database\Factories\Inventory;

use App\Models\Inventory\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'ruc' => fake()->unique()->numerify('2###########'),
            'social_reason' => fake()->company().' S.A.C.',
            'phone' => fake()->numerify('(0#) ###-####'),
            'contact_person' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'status' => true,
        ];
    }
}
