<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,

            // Configuración base
            UbigeoSeeder::class,
            TimezoneSeeder::class,
            CurrencySeeder::class,
            DocumentTypeSeeder::class,
            PaymentMethodSeeder::class,
            CompanySeeder::class,
            SunatConfigSeeder::class,

            // Restaurante
            HallSeeder::class,
            ShiftSeeder::class,
            CashRegisterSeeder::class,
            TableSeeder::class,

            // Inventario
            PurchaseCategorySeeder::class,
            ProductCategorySeeder::class,
            PeruvianMenuSeeder::class,
        ]);

        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Principal',
            'email' => 'admin@example.com',
            'username' => 'admin',
        ])->assignRole('administrador');
    }
}
