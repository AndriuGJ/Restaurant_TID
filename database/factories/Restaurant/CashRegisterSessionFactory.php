<?php

namespace Database\Factories\Restaurant;

use App\Models\Restaurant\CashRegister;
use App\Models\Restaurant\CashRegisterSession;
use App\Models\Restaurant\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashRegisterSession>
 */
class CashRegisterSessionFactory extends Factory
{
    protected $model = CashRegisterSession::class;

    public function definition(): array
    {
        return [
            'cash_register_id' => CashRegister::factory(),
            'shift_id' => Shift::factory(),
            'user_opening_id' => User::factory(),
            'user_closing_id' => null,
            'opening_amount' => fake()->randomFloat(2, 100, 1000),
            'closing_amount' => null,
            'status' => 'open',
            'opened_at' => now(),
            'closed_at' => null,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_closing_id' => User::factory(),
            'closing_amount' => fake()->randomFloat(2, 100, 2000),
            'status' => 'closed',
            'closed_at' => now()->addHours(8),
        ]);
    }
}
