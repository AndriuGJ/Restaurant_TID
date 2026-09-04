<?php

use App\Models\Customers\Customer;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['clientes-ver', 'clientes-gestionar'] as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['clientes-ver', 'clientes-gestionar']);
});

function createCustomerAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access customers index', function () {
    $this->get(route('customers.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access customers index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('customers.index'))
        ->assertForbidden();
});

test('admin can list customers', function () {
    Customer::factory()->create(['name' => 'Juan Pérez']);

    $this->actingAs(createCustomerAdmin())
        ->get(route('customers.index'))
        ->assertOk()
        ->assertSee('Juan Pérez');
});

test('admin can store a customer', function () {
    $this->actingAs(createCustomerAdmin())
        ->post(route('customers.store'), [
            'name' => 'Ana Gómez',
            'phone' => '987654321',
            'email' => 'ana@example.com',
            'status' => true,
        ])
        ->assertRedirect(route('customers.index'));

    $this->assertDatabaseHas('customers', ['name' => 'Ana Gómez']);
});

test('store customer requires a name', function () {
    $this->actingAs(createCustomerAdmin())
        ->post(route('customers.store'), ['name' => ''])
        ->assertSessionHasErrors('name');
});

test('store customer validates email format', function () {
    $this->actingAs(createCustomerAdmin())
        ->post(route('customers.store'), ['name' => 'Cliente', 'email' => 'no-email'])
        ->assertSessionHasErrors('email');
});

test('admin can update a customer', function () {
    $customer = Customer::factory()->create(['name' => 'Antiguo']);

    $this->actingAs(createCustomerAdmin())
        ->put(route('customers.update', $customer), [
            'name' => 'Nuevo Cliente',
            'status' => false,
        ])
        ->assertRedirect(route('customers.index'));

    $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'Nuevo Cliente', 'status' => 0]);
});

test('admin can delete a customer', function () {
    $customer = Customer::factory()->create();

    $this->actingAs(createCustomerAdmin())
        ->delete(route('customers.destroy', $customer))
        ->assertRedirect(route('customers.index'));

    $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
});
