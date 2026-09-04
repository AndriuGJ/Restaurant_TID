<?php

use App\Models\Restaurant\DeliveryProvider;
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

function createDeliveryProviderAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access delivery providers index', function () {
    $this->get(route('restaurant.delivery-providers.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access delivery providers index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('restaurant.delivery-providers.index'))
        ->assertForbidden();
});

test('admin can list delivery providers', function () {
    DeliveryProvider::factory()->create(['name' => 'Rappi']);

    $this->actingAs(createDeliveryProviderAdmin())
        ->get(route('restaurant.delivery-providers.index'))
        ->assertOk()
        ->assertSee('Rappi');
});

test('admin can store a delivery provider', function () {
    $this->actingAs(createDeliveryProviderAdmin())
        ->post(route('restaurant.delivery-providers.store'), [
            'name' => 'PedidosYa',
            'phone' => '999999999',
            'contact_person' => 'Juan Pérez',
            'status' => true,
        ])
        ->assertRedirect(route('restaurant.delivery-providers.index'));

    $this->assertDatabaseHas('delivery_providers', ['name' => 'PedidosYa']);
});

test('admin can update a delivery provider', function () {
    $provider = DeliveryProvider::factory()->create(['name' => 'Antiguo']);

    $this->actingAs(createDeliveryProviderAdmin())
        ->put(route('restaurant.delivery-providers.update', $provider), [
            'name' => 'Nuevo',
            'phone' => null,
            'contact_person' => null,
            'status' => false,
        ])
        ->assertRedirect(route('restaurant.delivery-providers.index'));

    $this->assertDatabaseHas('delivery_providers', ['id' => $provider->id, 'name' => 'Nuevo', 'status' => 0]);
});

test('admin can delete a delivery provider', function () {
    $provider = DeliveryProvider::factory()->create();

    $this->actingAs(createDeliveryProviderAdmin())
        ->delete(route('restaurant.delivery-providers.destroy', $provider))
        ->assertRedirect(route('restaurant.delivery-providers.index'));

    $this->assertDatabaseMissing('delivery_providers', ['id' => $provider->id]);
});
