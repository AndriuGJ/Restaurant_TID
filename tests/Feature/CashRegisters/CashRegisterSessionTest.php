<?php

use App\Models\Restaurant\CashRegister;
use App\Models\Restaurant\CashRegisterSession;
use App\Models\Restaurant\Shift;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['cajas-ver', 'cajas-abrir', 'cajas-cerrar'] as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['cajas-ver', 'cajas-abrir', 'cajas-cerrar']);
});

function createCajasAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access cash register sessions index', function () {
    $this->get(route('cash-registers.sessions.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access cash register sessions index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('cash-registers.sessions.index'))
        ->assertForbidden();
});

test('admin can list cash register sessions', function () {
    CashRegisterSession::factory()->create();

    $this->actingAs(createCajasAdmin())
        ->get(route('cash-registers.sessions.index'))
        ->assertOk();
});

test('admin can open a cash register', function () {
    $cashRegister = CashRegister::factory()->create();
    $shift = Shift::factory()->create();

    $this->actingAs(createCajasAdmin())
        ->post(route('cash-registers.sessions.store'), [
            'cash_register_id' => $cashRegister->id,
            'shift_id' => $shift->id,
            'opening_amount' => 500,
        ])
        ->assertRedirect(route('cash-registers.sessions.index'));

    $this->assertDatabaseHas('cash_register_sessions', [
        'cash_register_id' => $cashRegister->id,
        'status' => 'open',
        'opening_amount' => 500,
    ]);
});

test('cannot open a cash register that already has an open session', function () {
    $cashRegister = CashRegister::factory()->create();
    $shift = Shift::factory()->create();
    CashRegisterSession::factory()->create([
        'cash_register_id' => $cashRegister->id,
        'shift_id' => $shift->id,
        'status' => 'open',
    ]);

    $this->actingAs(createCajasAdmin())
        ->post(route('cash-registers.sessions.store'), [
            'cash_register_id' => $cashRegister->id,
            'shift_id' => $shift->id,
            'opening_amount' => 100,
        ])
        ->assertSessionHasErrors('cash_register_id');
});

test('opening amount is required', function () {
    $cashRegister = CashRegister::factory()->create();
    $shift = Shift::factory()->create();

    $this->actingAs(createCajasAdmin())
        ->post(route('cash-registers.sessions.store'), [
            'cash_register_id' => $cashRegister->id,
            'shift_id' => $shift->id,
            'opening_amount' => '',
        ])
        ->assertSessionHasErrors('opening_amount');
});

test('admin can close an open cash register', function () {
    $session = CashRegisterSession::factory()->create([
        'status' => 'open',
        'opening_amount' => 500,
        'opened_at' => now(),
    ]);

    $this->actingAs(createCajasAdmin())
        ->put(route('cash-registers.sessions.update', $session), [
            'closing_amount' => 750,
        ])
        ->assertRedirect(route('cash-registers.sessions.index'));

    $this->assertDatabaseHas('cash_register_sessions', [
        'id' => $session->id,
        'status' => 'closed',
        'closing_amount' => 750,
    ]);
});

test('closing amount is required', function () {
    $session = CashRegisterSession::factory()->create(['status' => 'open']);

    $this->actingAs(createCajasAdmin())
        ->put(route('cash-registers.sessions.update', $session), [
            'closing_amount' => '',
        ])
        ->assertSessionHasErrors('closing_amount');
});

test('admin can access the close view of an open session', function () {
    $session = CashRegisterSession::factory()->create(['status' => 'open']);

    $this->actingAs(createCajasAdmin())
        ->get(route('cash-registers.sessions.edit', $session))
        ->assertOk();
});

test('the open form prefills the previous closing amount', function () {
    $cashRegister = CashRegister::factory()->create();
    $previous = CashRegisterSession::factory()->closed()->create([
        'cash_register_id' => $cashRegister->id,
        'closing_amount' => 800.00,
    ]);

    $this->actingAs(createCajasAdmin())
        ->get(route('cash-registers.sessions.create'))
        ->assertOk()
        ->assertSee('800.00');
});

test('a closed session amount is carried forward when re-opening', function () {
    $cashRegister = CashRegister::factory()->create();
    $shift = Shift::factory()->create();
    $previous = CashRegisterSession::factory()->closed()->create([
        'cash_register_id' => $cashRegister->id,
        'closing_amount' => 800.00,
    ]);

    $this->actingAs(createCajasAdmin())
        ->post(route('cash-registers.sessions.store'), [
            'cash_register_id' => $cashRegister->id,
            'shift_id' => $shift->id,
            'opening_amount' => $previous->closing_amount,
        ])
        ->assertRedirect(route('cash-registers.sessions.index'));

    $this->assertDatabaseHas('cash_register_sessions', [
        'cash_register_id' => $cashRegister->id,
        'status' => 'open',
        'opening_amount' => 800.00,
    ]);
});
