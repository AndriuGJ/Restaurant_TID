<?php

namespace Database\Seeders;

use App\Models\Restaurant\Hall;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HallSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $halls = [
            ['name' => 'Salón Principal', 'status' => true],
            ['name' => 'Terraza', 'status' => true],
            ['name' => 'VIP', 'status' => true],
        ];

        foreach ($halls as $hall) {
            Hall::firstOrCreate(
                ['name' => $hall['name']],
                $hall
            );
        }
    }
}
