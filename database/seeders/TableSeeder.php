<?php

namespace Database\Seeders;

use App\Models\Restaurant\Hall;
use App\Models\Restaurant\Table;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $halls = Hall::all();

        if ($halls->isEmpty()) {
            return;
        }

        $shapes = ['square', 'round', 'rectangular'];
        $counter = 1;

        foreach ($halls as $hall) {
            // 6 mesas por salón
            for ($i = 1; $i <= 30; $i++) {
                $shape = $shapes[$i % count($shapes)];

                Table::firstOrCreate(
                    [
                        'hall_id' => $hall->id,
                        'name' => "Mesa {$counter}",
                    ],
                    [
                        'hall_id' => $hall->id,
                        'name' => "Mesa {$counter}",
                        'shape' => $shape,
                        'status' => 'available',
                    ]
                );

                $counter++;
            }
        }
    }
}
