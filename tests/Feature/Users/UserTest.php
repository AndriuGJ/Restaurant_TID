<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['usuarios-ver', 'usuarios-gestionar'] as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['usuarios-ver', 'usuarios-gestionar']);
});

function createUsersAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access users index', function () {
    $this->get(route('users.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access users index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('users.index'))
        ->assertForbidden();
});

test('admin can list users', function () {
    User::factory()->create(['first_name' => 'Pedro', 'last_name' => 'Pérez']);

    $this->actingAs(createUsersAdmin())
        ->get(route('users.index'))
        ->assertOk()
        ->assertSee('Pedro');
});

test('admin can store a user and assign a role', function () {
    $role = Role::firstOrCreate(['name' => 'mozo']);

    $this->actingAs(createUsersAdmin())
        ->post(route('users.store'), [
            'dni' => '12345678',
            'first_name' => 'Juan',
            'last_name' => 'Quispe',
            'email' => 'juan@example.com',
            'username' => 'juanq',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'cargo' => 'Cajero',
            'status' => 1,
            'roles' => [$role->name],
        ])
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', ['email' => 'juan@example.com']);
    $user = User::where('email', 'juan@example.com')->first();

    $this->assertTrue($user->hasRole('mozo'));
});

test('store user requires a unique email', function () {
    User::factory()->create(['email' => 'juan@example.com']);

    $this->actingAs(createUsersAdmin())
        ->post(route('users.store'), [
            'first_name' => 'Juan',
            'last_name' => 'Quispe',
            'email' => 'juan@example.com',
            'username' => 'juanx',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'roles' => ['mozo'],
        ])
        ->assertSessionHasErrors('email');
});

test('store user requires a password', function () {
    $this->actingAs(createUsersAdmin())
        ->post(route('users.store'), [
            'first_name' => 'Juan',
            'last_name' => 'Quispe',
            'email' => 'juan@example.com',
            'username' => 'juanx',
            'password' => '',
            'roles' => ['mozo'],
        ])
        ->assertSessionHasErrors('password');
});

test('admin can update a user and reassign roles', function () {
    $role = Role::firstOrCreate(['name' => 'mozo']);
    $user = User::factory()->create(['email' => 'juan@example.com']);

    $this->actingAs(createUsersAdmin())
        ->put(route('users.update', $user), [
            'first_name' => 'Juanito',
            'last_name' => 'Quispe',
            'email' => 'juan@example.com',
            'username' => $user->username,
            'password' => '',
            'roles' => [$role->name],
        ])
        ->assertRedirect(route('users.index'));

    $this->assertEquals('Juanito', $user->fresh()->first_name);
    $this->assertTrue($user->fresh()->hasRole('mozo'));
});

test('without a password the existing one is kept on update', function () {
    Role::firstOrCreate(['name' => 'mozo']);
    $originalHash = bcrypt('original-password');
    $user = User::factory()->create(['password' => $originalHash]);

    $this->actingAs(createUsersAdmin())
        ->put(route('users.update', $user), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'username' => $user->username,
            'password' => '',
            'roles' => ['mozo'],
        ])
        ->assertRedirect(route('users.index'));

    $this->assertEquals($originalHash, $user->fresh()->password);
});

test('admin can delete a user', function () {
    $user = User::factory()->create();

    $this->actingAs(createUsersAdmin())
        ->delete(route('users.destroy', $user))
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('cannot delete your own user', function () {
    $admin = createUsersAdmin();

    $this->actingAs($admin)
        ->delete(route('users.destroy', $admin))
        ->assertSessionHasErrors();
});
