<?php

namespace Database\Factories\Inventory;

use App\Models\Inventory\PurchaseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseCategory>
 */
class PurchaseCategoryFactory extends Factory
{
    protected $model = PurchaseCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Materia Prima', 'Insumo', 'Empaque', 'Utensilios']),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
