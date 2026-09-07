<?php

use App\Models\Restaurant\Printer;
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

function createPrinterAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access printers index', function () {
    $this->get(route('restaurant.printers.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access printers index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('restaurant.printers.index'))
        ->assertForbidden();
});

test('admin can list printers', function () {
    Printer::factory()->create(['name' => 'Cochera']);
    Printer::factory()->create(['name' => 'Caja Principal']);

    $this->actingAs(createPrinterAdmin())
        ->get(route('restaurant.printers.index'))
        ->assertOk()
        ->assertSee('Caja Principal')
        ->assertSee('Cochera');
});

test('admin can store a printer', function () {
    $this->actingAs(createPrinterAdmin())
        ->post(route('restaurant.printers.store'), [
            'name' => 'Caja Principal',
            'ip_address' => '192.168.1.50',
            'port' => 9100,
            'is_default' => true,
            'is_active' => true,
        ])
        ->assertRedirect(route('restaurant.printers.index'));

    $this->assertDatabaseHas('printers', [
        'name' => 'Caja Principal',
        'ip_address' => '192.168.1.50',
        'port' => 9100,
        'is_default' => true,
        'is_active' => true,
    ]);
});

test('admin can update a printer', function () {
    $printer = Printer::factory()->create(['name' => 'Antigua', 'ip_address' => '192.168.1.50']);

    $this->actingAs(createPrinterAdmin())
        ->put(route('restaurant.printers.update', $printer), [
            'name' => 'Nueva caja',
            'ip_address' => '192.168.1.60',
            'port' => 9100,
            'is_default' => true,
            'is_active' => true,
        ])
        ->assertRedirect(route('restaurant.printers.index'));

    $this->assertDatabaseHas('printers', [
        'id' => $printer->id,
        'name' => 'Nueva caja',
        'ip_address' => '192.168.1.60',
        'is_default' => true,
    ]);
});

test('only one printer can be the main cash register', function () {
    $first = Printer::factory()->create(['name' => 'Cochera', 'is_default' => true]);

    $this->actingAs(createPrinterAdmin())
        ->post(route('restaurant.printers.store'), [
            'name' => 'Caja Principal',
            'ip_address' => '192.168.1.50',
            'port' => 9100,
            'is_default' => true,
            'is_active' => true,
        ])
        ->assertRedirect(route('restaurant.printers.index'));

    $this->assertDatabaseHas('printers', ['id' => $first->id, 'is_default' => 0]);
    $this->assertDatabaseHas('printers', ['name' => 'Caja Principal', 'is_default' => 1]);
});

test('admin can delete a non-default printer', function () {
    $printer = Printer::factory()->create(['is_default' => false]);

    $this->actingAs(createPrinterAdmin())
        ->delete(route('restaurant.printers.destroy', $printer))
        ->assertRedirect(route('restaurant.printers.index'));

    $this->assertDatabaseMissing('printers', ['id' => $printer->id]);
});

test('the main cash register printer cannot be deleted while it is the only default', function () {
    $printer = Printer::factory()->create(['name' => 'Caja Principal', 'is_default' => true]);

    $this->actingAs(createPrinterAdmin())
        ->delete(route('restaurant.printers.destroy', $printer))
        ->assertSessionHasErrors();

    $this->assertDatabaseHas('printers', ['id' => $printer->id]);
});

test('user without permission cannot scan the network', function () {
    $this->actingAs(User::factory()->create())
        ->postJson(route('restaurant.printers.scan'), [
            'from' => '192.168.1.1',
            'to' => '192.168.1.1',
        ])
        ->assertForbidden();
});

test('scanning an invalid ip range is rejected', function () {
    $this->actingAs(createPrinterAdmin())
        ->postJson(route('restaurant.printers.scan'), [
            'from' => 'no-es-una-ip',
            'to' => '192.168.1.1',
        ])
        ->assertStatus(422);
});

test('scanning a range larger than 255 ip addresses is rejected', function () {
    $this->actingAs(createPrinterAdmin())
        ->postJson(route('restaurant.printers.scan'), [
            'from' => '192.168.1.1',
            'to' => '10.0.0.1',
        ])
        ->assertStatus(422);
});

test('admin can store a local (usb/cable) printer', function () {
    $this->actingAs(createPrinterAdmin())
        ->post(route('restaurant.printers.store'), [
            'name' => 'Ticket USB',
            'connection_type' => 'local',
            'queue_name' => 'EPSON_TM-T20II',
            'is_default' => false,
            'is_active' => true,
        ])
        ->assertRedirect(route('restaurant.printers.index'));

    $this->assertDatabaseHas('printers', [
        'name' => 'Ticket USB',
        'connection_type' => 'local',
        'queue_name' => 'EPSON_TM-T20II',
    ]);
});

test('a local printer can be saved without a queue name', function () {
    $this->actingAs(createPrinterAdmin())
        ->post(route('restaurant.printers.store'), [
            'name' => 'Ticket USB',
            'connection_type' => 'local',
            'is_default' => false,
        ])
        ->assertRedirect(route('restaurant.printers.index'));

    $this->assertDatabaseHas('printers', [
        'name' => 'Ticket USB',
        'connection_type' => 'local',
    ]);
});

test('admin can update a printer to local connection', function () {
    $printer = Printer::factory()->create(['name' => 'Antigua', 'connection_type' => 'network', 'ip_address' => '192.168.1.50']);

    $this->actingAs(createPrinterAdmin())
        ->put(route('restaurant.printers.update', $printer), [
            'name' => 'Ticket USB',
            'connection_type' => 'local',
            'queue_name' => 'XP-80C',
            'is_default' => true,
            'is_active' => true,
        ])
        ->assertRedirect(route('restaurant.printers.index'));

    $this->assertDatabaseHas('printers', [
        'id' => $printer->id,
        'connection_type' => 'local',
        'queue_name' => 'XP-80C',
    ]);
});

test('scanning the loopback range is rejected', function () {
    $this->actingAs(createPrinterAdmin())
        ->postJson(route('restaurant.printers.scan'), [
            'from' => '127.0.1.1',
            'to' => '127.0.1.20',
        ])
        ->assertStatus(422);
});

test('user without permission cannot list cups queues', function () {
    $this->actingAs(User::factory()->create())
        ->postJson(route('restaurant.printers.queues'))
        ->assertForbidden();
});

test('admin can save a local printer in pdf (driver) mode', function () {
    $this->actingAs(createPrinterAdmin())
        ->post(route('restaurant.printers.store'), [
            'name' => 'Epson L3250',
            'connection_type' => 'local',
            'queue_name' => 'EPSON_L3250',
            'send_raw' => '0',
            'is_default' => true,
            'is_active' => true,
        ])
        ->assertRedirect(route('restaurant.printers.index'));

    $this->assertDatabaseHas('printers', [
        'name' => 'Epson L3250',
        'connection_type' => 'local',
        'send_raw' => 0,
        'is_default' => 1,
    ]);
});

test('sending an unchecked send_raw checkbox defaults to thermal', function () {
    $this->actingAs(createPrinterAdmin())
        ->post(route('restaurant.printers.store'), [
            'name' => 'Ticket USB',
            'connection_type' => 'local',
            'queue_name' => 'XP-80C',
            'is_default' => false,
            'is_active' => true,
        ])
        ->assertRedirect(route('restaurant.printers.index'));

    $this->assertDatabaseHas('printers', [
        'name' => 'Ticket USB',
        'send_raw' => 1,
    ]);
});
