<?php

use App\Models\Inventory\Product;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'pos-preparacion']);

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['pos-preparacion']);
});

function createKitchenAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access the kitchen', function () {
    $this->get(route('pos.kitchen.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access the kitchen', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('pos.kitchen.index'))
        ->assertForbidden();
});

test('kitchen shows pending orders grouped by sale', function () {
    $sale = Sale::factory()->create();
    $productName = Product::factory()->dish()->create(['name' => 'Ceviche Clásico']);

    SaleDetail::factory()->create([
        'sale_id' => $sale->id,
        'product_id' => $productName->id,
        'kitchen_status' => 'pending',
    ]);

    $this->actingAs(createKitchenAdmin())
        ->get(route('pos.kitchen.index'))
        ->assertOk()
        ->assertSee('Ceviche Clásico')
        ->assertSee('Pedido #');
});

test('kitchen does not show completed or preparing details', function () {
    $sale = Sale::factory()->create();
    SaleDetail::factory()->completed()->create(['sale_id' => $sale->id]);

    $this->actingAs(createKitchenAdmin())
        ->get(route('pos.kitchen.index'))
        ->assertOk()
        ->assertDontSee('Pedido #');
});

test('starting a detail marks it as preparing', function () {
    $detail = SaleDetail::factory()->create();

    $this->actingAs(createKitchenAdmin())
        ->post(route('pos.kitchen.start', $detail))
        ->assertRedirect(route('pos.kitchen.index'));

    $detail = $detail->fresh();

    $this->assertSame('preparing', $detail->kitchen_status);
    $this->assertNotNull($detail->prep_started_at);
});

test('completing a detail marks it completed and reverts a preparing sale', function () {
    $sale = Sale::factory()->create(['status' => 'preparing']);
    $detail = SaleDetail::factory()->preparing()->create(['sale_id' => $sale->id]);

    $this->actingAs(createKitchenAdmin())
        ->post(route('pos.kitchen.complete', $detail))
        ->assertRedirect(route('pos.kitchen.index'));

    $detail = $detail->fresh();

    $this->assertSame('completed', $detail->kitchen_status);
    $this->assertNotNull($detail->prep_completed_at);
    $this->assertSame('pending', $sale->fresh()->status);
});

test('kitchen shows the delivery label for delivery orders', function () {
    $sale = Sale::factory()->create(['sale_type' => 'delivery']);
    $product = Product::factory()->dish()->create();
    SaleDetail::factory()->create([
        'sale_id' => $sale->id,
        'product_id' => $product->id,
        'kitchen_status' => 'pending',
    ]);

    $this->actingAs(createKitchenAdmin())
        ->get(route('pos.kitchen.index'))
        ->assertOk()
        ->assertSee('DELIVERY');
});
