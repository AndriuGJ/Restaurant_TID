<?php

namespace Database\Factories\Inventory;

use App\Models\Inventory\Product;
use App\Models\Inventory\ProductCategory;
use App\Models\Inventory\PurchaseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'price' => fake()->randomFloat(2, 5, 150),
            'cost_price' => fake()->randomFloat(2, 2, 80),
            'sale_price' => fake()->randomFloat(2, 5, 150),
            'image_url' => null,
            'stock' => 0,
            'unit_of_measure' => 'unidad',
            'product_category_id' => ProductCategory::factory(),
            'purchase_category_id' => null,
            'type' => 'supply',
            'is_pos_item' => true,
            'status' => true,
        ];
    }

    public function withCategory(): static
    {
        return $this->state(fn () => [
            'product_category_id' => ProductCategory::factory(),
            'purchase_category_id' => PurchaseCategory::factory(),
        ]);
    }

    public function dish(): static
    {
        return $this->state(fn () => [
            'type' => 'dish',
            'product_category_id' => ProductCategory::factory(),
            'purchase_category_id' => null,
        ])->afterCreating(function (Product $product) {
            $ingredient = Product::factory()->supply()->create();

            $product->ingredients()->create([
                'ingredient_id' => $ingredient->id,
                'quantity' => fake()->randomFloat(2, 0.5, 5),
            ]);
        });
    }

    public function supply(): static
    {
        return $this->state(fn () => [
            'type' => 'supply',
            'product_category_id' => null,
            'purchase_category_id' => PurchaseCategory::factory(),
        ]);
    }

    public function combo(): static
    {
        return $this->state(fn () => [
            'type' => 'combo',
            'product_category_id' => ProductCategory::factory(),
            'purchase_category_id' => null,
        ]);
    }
}
