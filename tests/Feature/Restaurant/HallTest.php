<?php

use App\Models\Restaurant\Hall;
use App\Models\Restaurant\Table;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

$permissions = [
    'configuracion-ver',
    'configuracion-editar',
    'pos-ver',
];

beforeEach(function () use ($permissions) {
    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }
});

function createHallAdmin(): User
{
    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['configuracion-ver', 'configuracion-editar']);

    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}

test('guest cannot access halls index', function () {
    $this->get(route('restaurant.halls.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access halls index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('restaurant.halls.index'))
        ->assertForbidden();
});

test('admin can list halls', function () {
    Hall::factory()->create(['name' => 'Terraza']);

    $this->actingAs(createHallAdmin())
        ->get(route('restaurant.halls.index'))
        ->assertOk()
        ->assertSee('Terraza');
});

test('admin can view the create form', function () {
    $this->actingAs(createHallAdmin())
        ->get(route('restaurant.halls.create'))
        ->assertOk();
});

test('admin can store a hall', function () {
    $this->actingAs(createHallAdmin())
        ->post(route('restaurant.halls.store'), [
            'name' => 'Terraza',
            'status' => true,
        ])
        ->assertRedirect(route('restaurant.halls.index'));

    $this->assertDatabaseHas('halls', ['name' => 'Terraza', 'status' => 1]);
});

test('store hall requires a unique name', function () {
    Hall::factory()->create(['name' => 'Terraza']);

    $this->actingAs(createHallAdmin())
        ->post(route('restaurant.halls.store'), [
            'name' => 'Terraza',
        ])
        ->assertSessionHasErrors('name');
});

test('admin can view the edit form', function () {
    $hall = Hall::factory()->create();

    $this->actingAs(createHallAdmin())
        ->get(route('restaurant.halls.edit', $hall))
        ->assertOk()
        ->assertSee($hall->name);
});

test('admin can update a hall', function () {
    $hall = Hall::factory()->create(['name' => 'Antiguo']);

    $this->actingAs(createHallAdmin())
        ->put(route('restaurant.halls.update', $hall), [
            'name' => 'Nuevo',
            'status' => false,
        ])
        ->assertRedirect(route('restaurant.halls.index'));

    $this->assertDatabaseHas('halls', [
        'id' => $hall->id,
        'name' => 'Nuevo',
        'status' => 0,
    ]);
});

test('admin can delete a hall without tables', function () {
    $hall = Hall::factory()->create();

    $this->actingAs(createHallAdmin())
        ->delete(route('restaurant.halls.destroy', $hall))
        ->assertRedirect(route('restaurant.halls.index'));

    $this->assertDatabaseMissing('halls', ['id' => $hall->id]);
});

test('admin cannot delete a hall that has tables', function () {
    $hall = Hall::factory()->create();
    Table::factory()->create(['hall_id' => $hall->id]);

    $this->actingAs(createHallAdmin())
        ->delete(route('restaurant.halls.destroy', $hall))
        ->assertRedirect(route('restaurant.halls.index'))
        ->assertSessionHasErrors();

    $this->assertDatabaseHas('halls', ['id' => $hall->id]);
});
