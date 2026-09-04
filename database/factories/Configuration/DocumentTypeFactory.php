<?php

namespace Database\Factories\Configuration;

use App\Models\Configuration\DocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentType>
 */
class DocumentTypeFactory extends Factory
{
    protected $model = DocumentType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'DNI',
                'RUC',
                'Boleta',
                'Factura',
                'Carné de Extranjería',
                'Pasaporte',
            ]),
            'nomenclature' => fake()->optional()->randomElement(['DNI', 'RUC', 'B', 'F']),
            'character_limit' => fake()->optional()->randomElement([8, 11]),
            'type' => fake()->randomElement(['identification', 'invoice']),
            'status' => true,
        ];
    }

    public function identification(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'DNI',
            'nomenclature' => 'DNI',
            'character_limit' => 8,
            'type' => 'identification',
        ]);
    }

    public function invoice(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Factura',
            'nomenclature' => 'F',
            'character_limit' => null,
            'type' => 'invoice',
        ]);
    }

    public function boleta(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Boleta',
            'nomenclature' => 'B',
            'character_limit' => null,
            'type' => 'invoice',
        ]);
    }
}
