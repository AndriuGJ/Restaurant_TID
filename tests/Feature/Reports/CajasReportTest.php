<?php

use App\Models\Restaurant\CashRegisterSession;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'reportes-cajas-ver']);

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['reportes-cajas-ver']);
});

function createCajasReportAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access the cajas report', function () {
    $this->get(route('reportes.cajas'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access the cajas report', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('reportes.cajas'))
        ->assertForbidden();
});

test('admin can see the cash register session history', function () {
    $session = CashRegisterSession::factory()->closed()->create([
        'opening_amount' => 500,
        'closing_amount' => 900,
    ]);

    $this->actingAs(createCajasReportAdmin())
        ->get(route('reportes.cajas'))
        ->assertOk()
        ->assertSee('500.00')
        ->assertSee('900.00');
});

test('the cajas report can be filtered by date range', function () {
    CashRegisterSession::factory()->closed()->create(['opening_amount' => 300, 'opened_at' => now()->subDays(20)]);
    CashRegisterSession::factory()->closed()->create(['opening_amount' => 400, 'opened_at' => now()]);

    $this->actingAs(createCajasReportAdmin())
        ->get(route('reportes.cajas', ['from' => now()->toDateString()]))
        ->assertOk();
});
