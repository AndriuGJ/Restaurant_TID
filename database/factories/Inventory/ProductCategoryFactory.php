<?php

namespace Database\Factories\Inventory;

use App\Models\Inventory\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Bebidas', 'Postres', 'Entradas', 'Parrillas', 'Ceviches']),
            'description' => fake()->optional()->sentence(),
            'status' => true,
        ];
    }
}
