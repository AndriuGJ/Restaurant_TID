<?php

namespace Database\Factories\Restaurant;

use App\Models\Restaurant\DeliveryProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryProvider>
 */
class DeliveryProviderFactory extends Factory
{
    protected $model = DeliveryProvider::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Rappi',
                'PedidosYa',
                'DiDi Food',
                'Delivery Propio',
            ]),
            'phone' => fake()->numerify('9########'),
            'contact_person' => fake()->name(),
            'status' => true,
        ];
    }
}
