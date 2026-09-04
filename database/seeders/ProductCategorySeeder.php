<?php

namespace Database\Seeders;

use App\Models\Inventory\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            ['name' => 'Bebidas', 'description' => 'Gaseosas, jugos, aguas y refrescos', 'status' => true],
            ['name' => 'Postres', 'description' => 'Dulces y postres', 'status' => true],
            ['name' => 'Comida Caliente', 'description' => 'Platos principales calientes', 'status' => true],
            ['name' => 'Comida Criolla', 'description' => 'Platos de la cocina peruana criolla', 'status' => true],
            ['name' => 'Entradas', 'description' => 'Entradas y aperitivos', 'status' => true],
            ['name' => 'Sopas', 'description' => 'Sopas y cremas', 'status' => true],
            ['name' => 'Ceviches', 'description' => 'Platos a base de pescado crudo', 'status' => true],
            ['name' => 'Parrillas', 'description' => 'Carnes a la parrilla', 'status' => true],
        ];

        foreach ($categories as $category) {
            ProductCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
