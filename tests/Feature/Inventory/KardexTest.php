<?php

use App\Models\Inventory\Product;
use App\Models\Kardex\KardexMovement;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'kardex-ver']);

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['kardex-ver']);
});

function createKardexAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access kardex index', function () {
    $this->get(route('inventory.kardex.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access kardex index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('inventory.kardex.index'))
        ->assertForbidden();
});

test('admin can list kardex movements', function () {
    $product = Product::factory()->supply()->create();
    KardexMovement::factory()->create(['product_id' => $product->id, 'balance' => 25]);

    $this->actingAs(createKardexAdmin())
        ->get(route('inventory.kardex.index'))
        ->assertOk()
        ->assertSee($product->name)
        ->assertSee('25.00');
});

test('kardex can be filtered by product', function () {
    $productA = Product::factory()->supply()->create(['name' => 'Papa amarilla']);
    $productB = Product::factory()->supply()->create(['name' => 'Cebolla roja']);
    KardexMovement::factory()->create(['product_id' => $productA->id, 'balance' => 111.11]);
    KardexMovement::factory()->create(['product_id' => $productB->id, 'balance' => 222.22]);

    $this->actingAs(createKardexAdmin())
        ->get(route('inventory.kardex.index', ['product' => $productA->id]))
        ->assertOk()
        ->assertSee('Papa amarilla')
        ->assertSee('111.11')
        ->assertDontSee('222.22');
});
