<?php

use App\Models\Inventory\Product;
use App\Models\Restaurant\CashRegisterSession;
use App\Models\Sales\Sale;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $permissions = [
        'pos-ver', 'pos-venta-rapida', 'pos-delivery', 'pos-preparacion',
        'reportes-ver', 'cajas-ver', 'inventario-ver', 'clientes-ver',
    ];
    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions($permissions);
});

function createDashboardAdmin(): User
{
    $user = User::factory()->create([
        'first_name' => 'Carlos',
        'last_name' => 'Perez',
    ]);
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access the dashboard', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

test('authenticated users can access the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

test('the dashboard shows today paid sales total', function () {
    Sale::factory()->paid()->create(['total' => 100]);
    Sale::factory()->paid()->create(['total' => 50]);

    $this->actingAs(createDashboardAdmin())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('150.00');
});

test('the dashboard warns about low stock products', function () {
    Product::factory()->create(['name' => 'Cebolla', 'stock' => 3, 'status' => true]);
    Product::factory()->create(['name' => 'Papa', 'stock' => 25, 'status' => true]);

    $this->actingAs(createDashboardAdmin())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Cebolla')
        ->assertDontSee('Papa', false);
});

test('the dashboard displays the open cash session status', function () {
    $session = CashRegisterSession::factory()->create(['status' => 'open', 'opening_amount' => 200]);

    $this->actingAs(createDashboardAdmin())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Abierta')
        ->assertSee('200.00');
});
