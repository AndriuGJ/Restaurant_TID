<?php

use App\Models\Configuration\Company;
use App\Models\Configuration\SunatConfig;
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

function createSunatConfigAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access sunat configs index', function () {
    $this->get(route('configuration.sunat-configs.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access sunat configs index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('configuration.sunat-configs.index'))
        ->assertForbidden();
});

test('admin can list sunat configs', function () {
    $company = Company::factory()->create(['name' => 'Restaurante Demo']);
    SunatConfig::factory()->create(['company_id' => $company->id, 'status' => 'active']);

    $this->actingAs(createSunatConfigAdmin())
        ->get(route('configuration.sunat-configs.index'))
        ->assertOk()
        ->assertSee('Restaurante Demo');
});

test('admin can store a sunat config', function () {
    $company = Company::factory()->create();

    $this->actingAs(createSunatConfigAdmin())
        ->post(route('configuration.sunat-configs.store'), [
            'company_id' => $company->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'status' => 'active',
            'max_receipts' => 1000,
            'used_receipts' => 0,
            'card_surcharge_percentage' => 2.50,
        ])
        ->assertRedirect(route('configuration.sunat-configs.index'));

    $this->assertDatabaseHas('sunat_configs', ['company_id' => $company->id, 'max_receipts' => 1000]);
});

test('store sunat config validates end date is after start date', function () {
    $company = Company::factory()->create();

    $this->actingAs(createSunatConfigAdmin())
        ->post(route('configuration.sunat-configs.store'), [
            'company_id' => $company->id,
            'start_date' => '2026-12-31',
            'end_date' => '2026-01-01',
            'status' => 'active',
            'max_receipts' => 1000,
            'used_receipts' => 0,
            'card_surcharge_percentage' => 0,
        ])
        ->assertSessionHasErrors('end_date');
});

test('admin can update a sunat config', function () {
    $company = Company::factory()->create();
    $sunatConfig = SunatConfig::factory()->create();

    $this->actingAs(createSunatConfigAdmin())
        ->put(route('configuration.sunat-configs.update', $sunatConfig), [
            'company_id' => $company->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'status' => 'expired',
            'max_receipts' => 500,
            'used_receipts' => 200,
            'card_surcharge_percentage' => 3.00,
        ])
        ->assertRedirect(route('configuration.sunat-configs.index'));

    $this->assertDatabaseHas('sunat_configs', ['id' => $sunatConfig->id, 'status' => 'expired', 'used_receipts' => 200]);
});

test('admin can delete a sunat config', function () {
    $sunatConfig = SunatConfig::factory()->create();

    $this->actingAs(createSunatConfigAdmin())
        ->delete(route('configuration.sunat-configs.destroy', $sunatConfig))
        ->assertRedirect(route('configuration.sunat-configs.index'));

    $this->assertDatabaseMissing('sunat_configs', ['id' => $sunatConfig->id]);
});
