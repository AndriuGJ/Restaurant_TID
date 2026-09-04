<?php

use App\Models\Configuration\PaymentMethod;
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

function createPaymentMethodAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access payment methods index', function () {
    $this->get(route('configuration.payment-methods.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access payment methods index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('configuration.payment-methods.index'))
        ->assertForbidden();
});

test('admin can list payment methods', function () {
    PaymentMethod::factory()->create(['name' => 'Yape']);

    $this->actingAs(createPaymentMethodAdmin())
        ->get(route('configuration.payment-methods.index'))
        ->assertOk()
        ->assertSee('Yape');
});

test('admin can store a payment method', function () {
    $this->actingAs(createPaymentMethodAdmin())
        ->post(route('configuration.payment-methods.store'), [
            'name' => 'Plin',
            'type' => 'digital',
            'status' => true,
        ])
        ->assertRedirect(route('configuration.payment-methods.index'));

    $this->assertDatabaseHas('payment_methods', ['name' => 'Plin', 'type' => 'digital']);
});

test('store payment method requires a unique name', function () {
    PaymentMethod::factory()->create(['name' => 'Plin']);

    $this->actingAs(createPaymentMethodAdmin())
        ->post(route('configuration.payment-methods.store'), [
            'name' => 'Plin',
            'type' => 'digital',
        ])
        ->assertSessionHasErrors('name');
});

test('admin can update a payment method', function () {
    $paymentMethod = PaymentMethod::factory()->create(['name' => 'Antiguo']);

    $this->actingAs(createPaymentMethodAdmin())
        ->put(route('configuration.payment-methods.update', $paymentMethod), [
            'name' => 'Nuevo',
            'type' => 'card',
            'status' => false,
        ])
        ->assertRedirect(route('configuration.payment-methods.index'));

    $this->assertDatabaseHas('payment_methods', ['id' => $paymentMethod->id, 'name' => 'Nuevo', 'type' => 'card']);
});

test('admin can delete a payment method', function () {
    $paymentMethod = PaymentMethod::factory()->create();

    $this->actingAs(createPaymentMethodAdmin())
        ->delete(route('configuration.payment-methods.destroy', $paymentMethod))
        ->assertRedirect(route('configuration.payment-methods.index'));

    $this->assertDatabaseMissing('payment_methods', ['id' => $paymentMethod->id]);
});
