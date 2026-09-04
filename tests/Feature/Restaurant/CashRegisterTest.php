<?php

use App\Models\Restaurant\CashRegister;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['configuracion-ver', 'configuracion-editar'] as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['configuracion-ver', 'configuracion-editar']);
});

function createCashRegisterAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access cash registers index', function () {
    $this->get(route('restaurant.cash-registers.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access cash registers index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('restaurant.cash-registers.index'))
        ->assertForbidden();
});

test('admin can list cash registers', function () {
    CashRegister::factory()->create(['name' => 'Caja Principal']);

    $this->actingAs(createCashRegisterAdmin())
        ->get(route('restaurant.cash-registers.index'))
        ->assertOk()
        ->assertSee('Caja Principal');
});

test('admin can store a cash register', function () {
    $this->actingAs(createCashRegisterAdmin())
        ->post(route('restaurant.cash-registers.store'), [
            'name' => 'Caja Barra',
            'status' => true,
        ])
        ->assertRedirect(route('restaurant.cash-registers.index'));

    $this->assertDatabaseHas('cash_registers', ['name' => 'Caja Barra', 'status' => 1]);
});

test('store cash register requires a unique name', function () {
    CashRegister::factory()->create(['name' => 'Caja Barra']);

    $this->actingAs(createCashRegisterAdmin())
        ->post(route('restaurant.cash-registers.store'), ['name' => 'Caja Barra'])
        ->assertSessionHasErrors('name');
});

test('admin can update a cash register', function () {
    $cashRegister = CashRegister::factory()->create(['name' => 'Antigua']);

    $this->actingAs(createCashRegisterAdmin())
        ->put(route('restaurant.cash-registers.update', $cashRegister), [
            'name' => 'Nueva',
            'status' => false,
        ])
        ->assertRedirect(route('restaurant.cash-registers.index'));

    $this->assertDatabaseHas('cash_registers', ['id' => $cashRegister->id, 'name' => 'Nueva', 'status' => 0]);
});

test('admin can delete a cash register', function () {
    $cashRegister = CashRegister::factory()->create();

    $this->actingAs(createCashRegisterAdmin())
        ->delete(route('restaurant.cash-registers.destroy', $cashRegister))
        ->assertRedirect(route('restaurant.cash-registers.index'));

    $this->assertDatabaseMissing('cash_registers', ['id' => $cashRegister->id]);
});
