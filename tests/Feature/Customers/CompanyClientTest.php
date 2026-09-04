<?php

use App\Models\Customers\CompanyClient;
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

function createCompanyClientAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access company clients index', function () {
    $this->get(route('customers.companies.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access company clients index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('customers.companies.index'))
        ->assertForbidden();
});

test('admin can list company clients', function () {
    CompanyClient::factory()->create(['social_reason' => 'Gastronomía SAC']);

    $this->actingAs(createCompanyClientAdmin())
        ->get(route('customers.companies.index'))
        ->assertOk()
        ->assertSee('Gastronomía SAC');
});

test('admin can store a company client', function () {
    $this->actingAs(createCompanyClientAdmin())
        ->post(route('customers.companies.store'), [
            'ruc' => '20123456789',
            'social_reason' => 'Gastronomía SAC',
            'status' => true,
        ])
        ->assertRedirect(route('customers.companies.index'));

    $this->assertDatabaseHas('company_clients', ['ruc' => '20123456789']);
});

test('store company client requires a unique ruc', function () {
    CompanyClient::factory()->create(['ruc' => '20123456789']);

    $this->actingAs(createCompanyClientAdmin())
        ->post(route('customers.companies.store'), [
            'ruc' => '20123456789',
            'social_reason' => 'Otra SAC',
        ])
        ->assertSessionHasErrors('ruc');
});

test('admin can update a company client', function () {
    $companyClient = CompanyClient::factory()->create(['social_reason' => 'Antigua']);

    $this->actingAs(createCompanyClientAdmin())
        ->put(route('customers.companies.update', $companyClient), [
            'ruc' => $companyClient->ruc,
            'social_reason' => 'Nueva SAC',
            'status' => true,
        ])
        ->assertRedirect(route('customers.companies.index'));

    $this->assertDatabaseHas('company_clients', ['id' => $companyClient->id, 'social_reason' => 'Nueva SAC']);
});

test('admin can delete a company client', function () {
    $companyClient = CompanyClient::factory()->create();

    $this->actingAs(createCompanyClientAdmin())
        ->delete(route('customers.companies.destroy', $companyClient))
        ->assertRedirect(route('customers.companies.index'));

    $this->assertDatabaseMissing('company_clients', ['id' => $companyClient->id]);
});
