<?php

use App\Models\Configuration\Company;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['configuracion-ver', 'configuracion-editar'] as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['configuracion-ver', 'configuracion-editar']);
});

function createCompanyAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access companies index', function () {
    $this->get(route('configuration.companies.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access companies index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('configuration.companies.index'))
        ->assertForbidden();
});

test('admin can view the single company detail', function () {
    Company::factory()->create(['name' => 'Restaurante Demo']);

    $this->actingAs(createCompanyAdmin())
        ->get(route('configuration.companies.index'))
        ->assertOk()
        ->assertSee('Restaurante Demo');
});

test('admin can update a company and keep sol password when left empty', function () {
    $company = Company::factory()->create([
        'name' => 'Antigua',
        'sol_password' => 'original-secret',
    ]);

    $this->actingAs(createCompanyAdmin())
        ->put(route('configuration.companies.update', $company), [
            'name' => 'Nueva',
            'commercial_name' => null,
            'phone' => null,
            'commercial_address' => null,
            'ruc' => $company->ruc,
            'social_reason' => $company->social_reason,
            'fiscal_address' => null,
            'sol_user' => null,
            'sol_password' => '',
            'digital_certificate_path' => null,
        ])
        ->assertRedirect(route('configuration.companies.index'));

    $this->assertDatabaseHas('companies', ['id' => $company->id, 'name' => 'Nueva']);

    $this->assertSame('original-secret', $company->fresh()->sol_password);
});

test('create and store routes for companies are no longer registered', function () {
    $this->assertFalse(Route::has('configuration.companies.create'));
    $this->assertFalse(Route::has('configuration.companies.store'));
    $this->assertFalse(Route::has('configuration.companies.destroy'));
});

test('update company rejects a logo larger than 2 MB', function () {
    Storage::fake('public');

    $company = Company::factory()->create();
    $oversized = UploadedFile::fake()->image('logo.png')->size(3000);

    $this->actingAs(createCompanyAdmin())
        ->put(route('configuration.companies.update', $company), [
            'name' => $company->name,
            'commercial_name' => null,
            'phone' => null,
            'commercial_address' => null,
            'ruc' => $company->ruc,
            'social_reason' => $company->social_reason,
            'fiscal_address' => null,
            'sol_user' => null,
            'sol_password' => '',
            'digital_certificate_path' => null,
            'logo' => $oversized,
        ])
        ->assertSessionHasErrors('logo');
});

test('update company does not change logo when no file is uploaded', function () {
    Storage::fake('public');

    $company = Company::factory()->create(['logo' => 'logos/old.png']);

    $this->actingAs(createCompanyAdmin())
        ->put(route('configuration.companies.update', $company), [
            'name' => $company->name,
            'commercial_name' => null,
            'phone' => null,
            'commercial_address' => null,
            'ruc' => $company->ruc,
            'social_reason' => $company->social_reason,
            'fiscal_address' => null,
            'sol_user' => null,
            'sol_password' => '',
            'digital_certificate_path' => null,
        ])
        ->assertRedirect(route('configuration.companies.index'));

    $this->assertSame('logos/old.png', $company->fresh()->logo);
});

test('update company deletes the previous logo when replacing it', function () {
    Storage::fake('public');

    $company = Company::factory()->create(['logo' => 'logos/old.png']);
    Storage::disk('public')->put('logos/old.png', 'old-content');
    $newLogo = UploadedFile::fake()->image('new.png');

    $this->actingAs(createCompanyAdmin())
        ->put(route('configuration.companies.update', $company), [
            'name' => $company->name,
            'commercial_name' => null,
            'phone' => null,
            'commercial_address' => null,
            'ruc' => $company->ruc,
            'social_reason' => $company->social_reason,
            'fiscal_address' => null,
            'sol_user' => null,
            'sol_password' => '',
            'digital_certificate_path' => null,
            'logo' => $newLogo,
        ])
        ->assertRedirect(route('configuration.companies.index'));

    $this->assertFileDoesNotExist(Storage::disk('public')->path('logos/old.png'));
    $this->assertNotSame('logos/old.png', $company->fresh()->logo);
    $this->assertStringStartsWith('logos/', $company->fresh()->logo);
});