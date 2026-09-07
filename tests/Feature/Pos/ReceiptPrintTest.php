<?php

use App\Models\Restaurant\CashRegisterSession;
use App\Models\Sales\Sale;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'pos-cobro']);

    $role = Role::firstOrCreate(['name' => 'cajero']);
    $role->syncPermissions(['pos-cobro']);
});

function createReceiptUser(): User
{
    $user = User::factory()->create();
    $user->assignRole('cajero');

    return $user;
}

function createPaidSale(): Sale
{
    CashRegisterSession::factory()->create(['status' => 'open']);

    return Sale::factory()->paid()->create([
        'series' => 'F001',
        'number' => '00000001',
    ]);
}

test('guest cannot print a receipt on the network printer', function () {
    $sale = createPaidSale();

    $this->post(route('pos.sale.receipt.print', $sale))
        ->assertRedirect(route('login'));
});

test('user without permission cannot print a receipt', function () {
    $sale = createPaidSale();

    $this->actingAs(User::factory()->create())
        ->post(route('pos.sale.receipt.print', $sale))
        ->assertForbidden();
});

test('printing a receipt without a main cash printer shows an error', function () {
    $sale = createPaidSale();

    $this->actingAs(createReceiptUser())
        ->post(route('pos.sale.receipt.print', $sale))
        ->assertRedirect(route('pos.sale.receipt', $sale))
        ->assertSessionHasErrors();
});
