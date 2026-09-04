<?php

use App\Models\Restaurant\Shift;
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

function createShiftAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access shifts index', function () {
    $this->get(route('restaurant.shifts.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access shifts index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('restaurant.shifts.index'))
        ->assertForbidden();
});

test('admin can list shifts', function () {
    Shift::factory()->create(['name' => 'Noche']);

    $this->actingAs(createShiftAdmin())
        ->get(route('restaurant.shifts.index'))
        ->assertOk()
        ->assertSee('Noche');
});

test('admin can store a shift', function () {
    $this->actingAs(createShiftAdmin())
        ->post(route('restaurant.shifts.store'), [
            'name' => 'Mañana',
            'status' => true,
        ])
        ->assertRedirect(route('restaurant.shifts.index'));

    $this->assertDatabaseHas('shifts', ['name' => 'Mañana', 'status' => 1]);
});

test('store shift requires a unique name', function () {
    Shift::factory()->create(['name' => 'Mañana']);

    $this->actingAs(createShiftAdmin())
        ->post(route('restaurant.shifts.store'), ['name' => 'Mañana'])
        ->assertSessionHasErrors('name');
});

test('admin can update a shift', function () {
    $shift = Shift::factory()->create(['name' => 'Antiguo']);

    $this->actingAs(createShiftAdmin())
        ->put(route('restaurant.shifts.update', $shift), [
            'name' => 'Nuevo',
            'status' => false,
        ])
        ->assertRedirect(route('restaurant.shifts.index'));

    $this->assertDatabaseHas('shifts', ['id' => $shift->id, 'name' => 'Nuevo', 'status' => 0]);
});

test('admin can delete a shift', function () {
    $shift = Shift::factory()->create();

    $this->actingAs(createShiftAdmin())
        ->delete(route('restaurant.shifts.destroy', $shift))
        ->assertRedirect(route('restaurant.shifts.index'));

    $this->assertDatabaseMissing('shifts', ['id' => $shift->id]);
});
