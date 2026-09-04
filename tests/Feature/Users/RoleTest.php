<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['usuarios-ver', 'usuarios-gestionar', 'roles-gestionar', 'pos-ver'] as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['usuarios-ver', 'usuarios-gestionar', 'roles-gestionar']);
});

function createRolesAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access roles index', function () {
    $this->get(route('users.roles.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access roles index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('users.roles.index'))
        ->assertForbidden();
});

test('admin can list roles', function () {
    Role::firstOrCreate(['name' => 'cajero']);

    $this->actingAs(createRolesAdmin())
        ->get(route('users.roles.index'))
        ->assertOk();
});

test('admin can access the role create form', function () {
    $this->actingAs(createRolesAdmin())
        ->get(route('users.roles.create'))
        ->assertOk();
});

test('admin can access the role edit form', function () {
    $role = Role::firstOrCreate(['name' => 'cajero']);

    $this->actingAs(createRolesAdmin())
        ->get(route('users.roles.edit', $role))
        ->assertOk();
});

test('admin can store a role with permissions', function () {
    $perm = Permission::firstOrCreate(['name' => 'pos-ver']);

    $this->actingAs(createRolesAdmin())
        ->post(route('users.roles.store'), [
            'name' => 'Cajero',
            'permissions' => [$perm->name],
        ])
        ->assertRedirect(route('users.roles.index'));

    $role = Role::where('name', 'cajero')->first();

    $this->assertNotNull($role);
    $this->assertTrue($role->hasPermissionTo('pos-ver'));
});

test('store role requires a unique name', function () {
    Role::firstOrCreate(['name' => 'chef']);

    $this->actingAs(createRolesAdmin())
        ->post(route('users.roles.store'), [
            'name' => 'chef',
        ])
        ->assertSessionHasErrors('name');
});

test('admin can update a role permissions', function () {
    $role = Role::firstOrCreate(['name' => 'cajero']);
    $perm = Permission::firstOrCreate(['name' => 'pos-ver']);

    $this->actingAs(createRolesAdmin())
        ->put(route('users.roles.update', $role), [
            'name' => 'Cajero',
            'permissions' => [$perm->name],
        ])
        ->assertRedirect(route('users.roles.index'));

    $this->assertTrue($role->fresh()->hasPermissionTo('pos-ver'));
});

test('the administrador role cannot be deleted', function () {
    $role = Role::firstOrCreate(['name' => 'administrador']);

    $this->actingAs(createRolesAdmin())
        ->delete(route('users.roles.destroy', $role))
        ->assertSessionHasErrors();

    $this->assertDatabaseHas('roles', ['name' => 'administrador']);
});

test('admin can delete a non-administrador role', function () {
    $role = Role::firstOrCreate(['name' => 'cajero']);

    $this->actingAs(createRolesAdmin())
        ->delete(route('users.roles.destroy', $role))
        ->assertRedirect(route('users.roles.index'));

    $this->assertDatabaseMissing('roles', ['id' => $role->id]);
});
