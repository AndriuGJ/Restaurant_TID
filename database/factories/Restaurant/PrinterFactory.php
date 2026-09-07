<?php

namespace Database\Factories\Restaurant;

use App\Models\Restaurant\Printer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Printer>
 */
class PrinterFactory extends Factory
{
    protected $model = Printer::class;

    public function definition(): array
    {
        return [
            'name' => 'Caja Principal',
            'ip_address' => fake()->ipv4(),
            'port' => 9100,
            'send_raw' => true,
            'is_default' => true,
            'is_active' => true,
        ];
    }
}
