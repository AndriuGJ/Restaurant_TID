<?php

namespace Database\Seeders;

use App\Models\Restaurant\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $shifts = [
            ['name' => 'Mañana', 'status' => true],
            ['name' => 'Tarde', 'status' => true],
            ['name' => 'Noche', 'status' => true],
        ];

        foreach ($shifts as $shift) {
            Shift::firstOrCreate(
                ['name' => $shift['name']],
                $shift
            );
        }
    }
}
