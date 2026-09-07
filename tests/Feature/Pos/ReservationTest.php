<?php

use App\Models\Restaurant\CashRegisterSession;
use App\Models\Restaurant\Reservation;
use App\Models\Restaurant\Table;
use App\Models\Sales\Sale;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['pos-ver', 'pos-ventas'] as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    $role = Role::firstOrCreate(['name' => 'cajero']);
    $role->syncPermissions(['pos-ver', 'pos-ventas']);
});

function createReservationUser(): User
{
    $user = User::factory()->create();
    $user->assignRole('cajero');

    return $user;
}

test('guest cannot reserve a table', function () {
    $table = Table::factory()->create();

    $this->post(route('pos.tables.reserve', $table), [
        'customer_name' => 'Juan Pérez',
        'people_count' => 4,
    ])->assertRedirect(route('login'));
});

test('user without permission cannot reserve a table', function () {
    $table = Table::factory()->create();

    $this->actingAs(User::factory()->create())
        ->post(route('pos.tables.reserve', $table), [
            'customer_name' => 'Juan Pérez',
            'people_count' => 4,
        ])->assertForbidden();
});

test('cashier can reserve a table', function () {
    $table = Table::factory()->create(['status' => 'available']);

    $this->actingAs(createReservationUser())
        ->post(route('pos.tables.reserve', $table), [
            'customer_name' => 'Juan Pérez',
            'people_count' => 4,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('reservations', [
        'table_id' => $table->id,
        'customer_name' => 'Juan Pérez',
        'people_count' => 4,
        'status' => 'active',
    ]);

    $this->assertDatabaseHas('tables', ['id' => $table->id, 'status' => 'reserved']);
});

test('reserve requires customer name and people count', function () {
    $table = Table::factory()->create();

    $this->actingAs(createReservationUser())
        ->post(route('pos.tables.reserve', $table), [])
        ->assertSessionHasErrors(['customer_name', 'people_count']);
});

test('cannot reserve an occupied table', function () {
    $table = Table::factory()->create(['status' => 'occupied']);

    $this->actingAs(createReservationUser())
        ->post(route('pos.tables.reserve', $table), [
            'customer_name' => 'Juan Pérez',
            'people_count' => 4,
        ])
        ->assertSessionHasErrors('error');
});

test('cashier can cancel a reservation and free the table', function () {
    $table = Table::factory()->create(['status' => 'reserved']);
    Reservation::factory()->create([
        'table_id' => $table->id,
        'customer_name' => 'Juan Pérez',
        'people_count' => 4,
        'status' => 'active',
    ]);

    $this->actingAs(createReservationUser())
        ->delete(route('pos.tables.reservations.cancel', $table))
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('reservations', ['table_id' => $table->id, 'status' => 'cancelled']);
    $this->assertDatabaseHas('tables', ['id' => $table->id, 'status' => 'available']);
});

test('opening a sale on a reserved table fulfills the reservation and occupies the table', function () {
    CashRegisterSession::factory()->create(['status' => 'open']);
    $table = Table::factory()->create(['status' => 'reserved']);
    $reservation = Reservation::factory()->create([
        'table_id' => $table->id,
        'customer_name' => 'Juan Pérez',
        'people_count' => 4,
        'status' => 'active',
    ]);

    $this->actingAs(createReservationUser())
        ->get(route('pos.open', $table))
        ->assertRedirect(route('pos.sale', Sale::latest('id')->first()));

    $this->assertDatabaseHas('reservations', ['id' => $reservation->id, 'status' => 'fulfilled']);
    $this->assertDatabaseHas('tables', ['id' => $table->id, 'status' => 'occupied']);
});
