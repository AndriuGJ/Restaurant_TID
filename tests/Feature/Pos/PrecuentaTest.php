<?php

use App\Models\Restaurant\CashRegisterSession;
use App\Models\Sales\Sale;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('formats whole quantities without decimals', function () {
    expect(format_quantity(1.00))->toBe('1')
        ->and(format_quantity(3))->toBe('3')
        ->and(format_quantity('2.00'))->toBe('2');
});

it('keeps useful decimals on fractional quantities', function () {
    expect(format_quantity(0.5))->toBe('0.5')
        ->and(format_quantity(1.75))->toBe('1.75');
});

function createPrecuentaUser(): User
{
    Permission::firstOrCreate(['name' => 'pos-ventas']);

    $role = Role::firstOrCreate(['name' => 'cajero']);
    $role->syncPermissions(['pos-ventas']);

    $user = User::factory()->create();
    $user->assignRole('cajero');

    return $user;
}

it('shows the precuenta summary for a sale', function () {
    CashRegisterSession::factory()->create(['status' => 'open']);

    $sale = Sale::factory()->completed()->create();

    $this->actingAs(createPrecuentaUser())
        ->get(route('pos.sale.precuenta', $sale))
        ->assertOk()
        ->assertSee('PRECUENTA', false)
        ->assertSee('Pedido: #'.$sale->id)
        ->assertSee(number_format($sale->total, 2));
});

it('requires login to view the precuenta', function () {
    $sale = Sale::factory()->completed()->create();

    $this->get(route('pos.sale.precuenta', $sale))
        ->assertRedirect(route('login'));
});

it('does not show the precuenta without an open pos session', function () {
    $sale = Sale::factory()->completed()->create();

    $this->actingAs(createPrecuentaUser())
        ->get(route('pos.sale.precuenta', $sale))
        ->assertRedirect(route('pos.requires-session'));
});
