<?php

namespace Database\Seeders;

use App\Models\Inventory\PurchaseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            ['name' => 'Materia Prima', 'description' => 'Insumos base para la preparación de platos'],
            ['name' => 'Insumo', 'description' => 'Insumos complementarios y no perecederos'],
        ];

        foreach ($categories as $category) {
            PurchaseCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
