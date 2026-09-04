<?php

namespace Database\Seeders;

use App\Models\Configuration\Timezone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimezoneSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $timezones = [
            ['name' => 'Lima', 'offset' => '-05:00'],
            ['name' => 'Bogotá', 'offset' => '-05:00'],
            ['name' => 'Quito', 'offset' => '-05:00'],
        ];

        foreach ($timezones as $timezone) {
            Timezone::firstOrCreate(
                ['name' => $timezone['name']],
                $timezone
            );
        }
    }
}
