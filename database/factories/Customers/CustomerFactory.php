<?php

namespace Database\Factories\Customers;

use App\Models\Configuration\DocumentType;
use App\Models\Customers\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->numerify('9########'),
            'email' => fake()->safeEmail(),
            'reference_address' => fake()->optional()->streetAddress(),
            'document_type_id' => DocumentType::factory()->identification(),
            'document_number' => fake()->numerify('########'),
            'status' => true,
        ];
    }
}
