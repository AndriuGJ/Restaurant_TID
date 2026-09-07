<?php

namespace Database\Factories\Restaurant;

use App\Models\Restaurant\Reservation;
use App\Models\Restaurant\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'table_id' => Table::factory(),
            'customer_name' => fake()->name(),
            'people_count' => fake()->numberBetween(1, 12),
            'user_id' => User::factory(),
            'status' => 'active',
        ];
    }
}
