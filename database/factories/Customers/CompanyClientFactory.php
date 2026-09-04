<?php

namespace Database\Factories\Customers;

use App\Models\Configuration\DocumentType;
use App\Models\Customers\CompanyClient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyClient>
 */
class CompanyClientFactory extends Factory
{
    protected $model = CompanyClient::class;

    public function definition(): array
    {
        return [
            'ruc' => fake()->unique()->numerify('2###########'),
            'social_reason' => fake()->company().' S.A.C.',
            'phone' => fake()->numerify('(0#) ###-####'),
            'contact_person' => fake()->name(),
            'document_type_id' => DocumentType::factory()->identification(),
            'status' => true,
        ];
    }
}
