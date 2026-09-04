<?php

namespace Database\Factories\Configuration;

use App\Models\Configuration\Company;
use App\Models\Configuration\Ubigeo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'commercial_name' => fake()->company(),
            'phone' => fake()->numerify('(0#) ###-####'),
            'commercial_address' => fake()->streetAddress(),
            'ruc' => fake()->unique()->numerify('2###########'),
            'social_reason' => fake()->company(),
            'fiscal_address' => fake()->streetAddress(),
            'ubigeo_id' => Ubigeo::factory(),
            'logo' => null,
            'sol_user' => fake()->numerify('##########'),
            'sol_password' => 'sol-password',
            'digital_certificate_path' => null,
        ];
    }
}
