<?php

use App\Models\Customers\Customer;
use App\Models\Sales\Sale;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'reportes-ver']);

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['reportes-ver']);
});

function createVentasReportAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access the ventas report', function () {
    $this->get(route('reportes.ventas'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access the ventas report', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('reportes.ventas'))
        ->assertForbidden();
});

test('admin can see paid sales from pos, delivery and quick sale', function () {
    $customer = Customer::factory()->create();

    Sale::factory()->paid()->create(['sale_type' => 'pos', 'clientable_type' => $customer->getMorphClass(), 'clientable_id' => $customer->id, 'total' => 100]);
    Sale::factory()->paid()->create(['sale_type' => 'delivery', 'total' => 50]);
    Sale::factory()->paid()->create(['sale_type' => 'quick_sale', 'total' => 30]);

    $this->actingAs(createVentasReportAdmin())
        ->get(route('reportes.ventas'))
        ->assertOk()
        ->assertSee('Delivery')
        ->assertSee('Venta rápida')
        ->assertSee('180.00');
});

test('a pending sale is not included in the ventas report totals', function () {
    $sale = Sale::factory()->create(['sale_type' => 'pos', 'status' => 'pending', 'total' => 999]);

    $this->actingAs(createVentasReportAdmin())
        ->get(route('reportes.ventas'))
        ->assertOk()
        ->assertDontSee('999.00');
});

test('the ventas report can be filtered by sale type', function () {
    Sale::factory()->paid()->create(['sale_type' => 'pos', 'total' => 100]);
    Sale::factory()->paid()->create(['sale_type' => 'delivery', 'total' => 50]);

    $this->actingAs(createVentasReportAdmin())
        ->get(route('reportes.ventas', ['sale_type' => 'delivery']))
        ->assertOk();
});
