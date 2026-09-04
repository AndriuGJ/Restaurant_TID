<?php

namespace Database\Factories\Configuration;

use App\Models\Configuration\Company;
use App\Models\Configuration\SunatConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SunatConfig>
 */
class SunatConfigFactory extends Factory
{
    protected $model = SunatConfig::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'start_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+1 year'),
            'status' => fake()->randomElement(['active', 'inactive', 'expired']),
            'max_receipts' => fake()->numberBetween(100, 10000),
            'used_receipts' => fake()->numberBetween(0, 500),
            'card_surcharge_percentage' => fake()->randomFloat(2, 0, 10),
        ];
    }
}
