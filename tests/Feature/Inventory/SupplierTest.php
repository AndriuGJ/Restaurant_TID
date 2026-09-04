<?php

use App\Models\Inventory\Supplier;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'inventario-ver']);

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['inventario-ver']);
});

function createSupplierAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access suppliers index', function () {
    $this->get(route('inventory.suppliers.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access suppliers index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('inventory.suppliers.index'))
        ->assertForbidden();
});

test('admin can list suppliers', function () {
    Supplier::factory()->create(['social_reason' => 'Distribuidora Norte SAC']);

    $this->actingAs(createSupplierAdmin())
        ->get(route('inventory.suppliers.index'))
        ->assertOk()
        ->assertSee('Distribuidora Norte SAC');
});

test('admin can store a supplier', function () {
    $this->actingAs(createSupplierAdmin())
        ->post(route('inventory.suppliers.store'), [
            'ruc' => '20123456789',
            'social_reason' => 'Distribuidora Norte SAC',
            'status' => true,
        ])
        ->assertRedirect(route('inventory.suppliers.index'));

    $this->assertDatabaseHas('suppliers', ['ruc' => '20123456789']);
});

test('store supplier requires a unique ruc', function () {
    Supplier::factory()->create(['ruc' => '20123456789']);

    $this->actingAs(createSupplierAdmin())
        ->post(route('inventory.suppliers.store'), [
            'ruc' => '20123456789',
            'social_reason' => 'Otra SAC',
        ])
        ->assertSessionHasErrors('ruc');
});

test('admin can update a supplier', function () {
    $supplier = Supplier::factory()->create(['social_reason' => 'Antigua']);

    $this->actingAs(createSupplierAdmin())
        ->put(route('inventory.suppliers.update', $supplier), [
            'ruc' => $supplier->ruc,
            'social_reason' => 'Nueva SAC',
            'status' => true,
        ])
        ->assertRedirect(route('inventory.suppliers.index'));

    $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'social_reason' => 'Nueva SAC']);
});

test('admin can delete a supplier', function () {
    $supplier = Supplier::factory()->create();

    $this->actingAs(createSupplierAdmin())
        ->delete(route('inventory.suppliers.destroy', $supplier))
        ->assertRedirect(route('inventory.suppliers.index'));

    $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
});
