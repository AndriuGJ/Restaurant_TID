<?php

use App\Models\Restaurant\Hall;
use App\Models\Restaurant\Table;
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

function createTableAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access tables index', function () {
    $this->get(route('restaurant.tables.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access tables index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('restaurant.tables.index'))
        ->assertForbidden();
});

test('admin can list tables', function () {
    $hall = Hall::factory()->create(['name' => 'VIP']);
    Table::factory()->create(['hall_id' => $hall->id, 'name' => 'Mesa 1']);

    $this->actingAs(createTableAdmin())
        ->get(route('restaurant.tables.index'))
        ->assertOk()
        ->assertSee('Mesa 1')
        ->assertSee('VIP');
});

test('admin can view the create form', function () {
    Hall::factory()->create(['name' => 'VIP']);

    $this->actingAs(createTableAdmin())
        ->get(route('restaurant.tables.create'))
        ->assertOk()
        ->assertSee('VIP');
});

test('admin can store a table', function () {
    $hall = Hall::factory()->create();

    $this->actingAs(createTableAdmin())
        ->post(route('restaurant.tables.store'), [
            'hall_id' => $hall->id,
            'name' => 'Mesa 1',
            'shape' => 'square',
            'status' => 'available',
        ])
        ->assertRedirect(route('restaurant.tables.index'));

    $this->assertDatabaseHas('tables', ['hall_id' => $hall->id, 'name' => 'Mesa 1']);
});

test('store table requires an existing hall', function () {
    $this->actingAs(createTableAdmin())
        ->post(route('restaurant.tables.store'), [
            'hall_id' => 9999,
            'name' => 'Mesa 1',
            'shape' => 'square',
            'status' => 'available',
        ])
        ->assertSessionHasErrors('hall_id');
});

test('store table requires a unique name within the hall', function () {
    $hall = Hall::factory()->create();
    Table::factory()->create(['hall_id' => $hall->id, 'name' => 'Mesa 1']);

    $this->actingAs(createTableAdmin())
        ->post(route('restaurant.tables.store'), [
            'hall_id' => $hall->id,
            'name' => 'Mesa 1',
            'shape' => 'square',
            'status' => 'available',
        ])
        ->assertSessionHasErrors('name');
});

test('admin can update a table', function () {
    $hall = Hall::factory()->create();
    $table = Table::factory()->create(['hall_id' => $hall->id, 'name' => 'Mesa Antigua']);

    $this->actingAs(createTableAdmin())
        ->put(route('restaurant.tables.update', $table), [
            'hall_id' => $hall->id,
            'name' => 'Mesa Nueva',
            'shape' => 'round',
            'status' => 'occupied',
        ])
        ->assertRedirect(route('restaurant.tables.index'));

    $this->assertDatabaseHas('tables', ['id' => $table->id, 'name' => 'Mesa Nueva', 'shape' => 'round', 'status' => 'occupied']);
});

test('admin can delete a table', function () {
    $table = Table::factory()->create();

    $this->actingAs(createTableAdmin())
        ->delete(route('restaurant.tables.destroy', $table))
        ->assertRedirect(route('restaurant.tables.index'));

    $this->assertDatabaseMissing('tables', ['id' => $table->id]);
});
