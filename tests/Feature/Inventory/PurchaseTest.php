<?php

use App\Models\Configuration\DocumentType;
use App\Models\Inventory\Product;
use App\Models\Inventory\Supplier;
use App\Models\Purchases\Purchase;
use App\Models\Purchases\PurchaseDetail;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'inventario-ver']);
    Permission::firstOrCreate(['name' => 'compras-gestionar']);

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['inventario-ver', 'compras-gestionar']);
});

function createPurchaseAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access purchases index', function () {
    $this->get(route('inventory.purchases.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access purchases index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('inventory.purchases.index'))
        ->assertForbidden();
});

test('admin can list purchases', function () {
    Purchase::factory()->create(['number' => '000123']);

    $this->actingAs(createPurchaseAdmin())
        ->get(route('inventory.purchases.index'))
        ->assertOk()
        ->assertSee('000123');
});

test('admin can store a purchase and registers kardex and stock', function () {
    $supplier = Supplier::factory()->create();
    $documentType = DocumentType::factory()->invoice()->create();
    $product = Product::factory()->supply()->create(['stock' => 0]);

    $this->actingAs(createPurchaseAdmin())
        ->post(route('inventory.purchases.store'), [
            'supplier_id' => $supplier->id,
            'document_type_id' => $documentType->id,
            'purchase_type' => 'contado',
            'series' => 'F001',
            'number' => '000999',
            'purchase_date' => now()->format('Y-m-d'),
            'details' => [
                ['product_id' => $product->id, 'quantity' => 10, 'unit_price' => 5.00],
            ],
        ])
        ->assertRedirect(route('inventory.purchases.index'));

    $purchase = Purchase::where('number', '000999')->first();

    $this->assertNotNull($purchase);
    $this->assertSame(50.0, (float) $purchase->total);
    $this->assertSame(50.0, (float) $purchase->subtotal);

    $this->assertDatabaseHas('purchase_details', [
        'purchase_id' => $purchase->id,
        'product_id' => $product->id,
        'quantity' => 10,
        'unit_price' => 5.00,
        'subtotal' => 50.00,
    ]);

    $this->assertSame(10.0, (float) $product->fresh()->stock);

    $this->assertDatabaseHas('kardex_movements', [
        'product_id' => $product->id,
        'movement_type' => 'purchase',
        'quantity_in' => 10,
        'quantity_out' => 0,
        'related_document_type' => Purchase::class,
        'related_document_id' => $purchase->id,
    ]);
});

test('store purchase requires an invoice document type', function () {
    $supplier = Supplier::factory()->create();
    $documentType = DocumentType::factory()->identification()->create();
    $product = Product::factory()->supply()->create();

    $this->actingAs(createPurchaseAdmin())
        ->post(route('inventory.purchases.store'), [
            'supplier_id' => $supplier->id,
            'document_type_id' => $documentType->id,
            'purchase_type' => 'contado',
            'number' => '000111',
            'purchase_date' => now()->format('Y-m-d'),
            'details' => [
                ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 1.00],
            ],
        ])
        ->assertSessionHasErrors('document_type_id');
});

test('store purchase requires at least one detail', function () {
    $supplier = Supplier::factory()->create();
    $documentType = DocumentType::factory()->invoice()->create();

    $this->actingAs(createPurchaseAdmin())
        ->post(route('inventory.purchases.store'), [
            'supplier_id' => $supplier->id,
            'document_type_id' => $documentType->id,
            'purchase_type' => 'contado',
            'number' => '000222',
            'purchase_date' => now()->format('Y-m-d'),
            'details' => [],
        ])
        ->assertSessionHasErrors('details');
});

test('admin can view a purchase', function () {
    $purchase = Purchase::factory()->create(['number' => '000333']);
    PurchaseDetail::factory()->create(['purchase_id' => $purchase->id]);

    $this->actingAs(createPurchaseAdmin())
        ->get(route('inventory.purchases.show', $purchase))
        ->assertOk()
        ->assertSee('000333');
});

test('store purchase accumulates totals across multiple details', function () {
    $supplier = Supplier::factory()->create();
    $documentType = DocumentType::factory()->invoice()->create();
    $productA = Product::factory()->supply()->create();
    $productB = Product::factory()->supply()->create();

    $this->actingAs(createPurchaseAdmin())
        ->post(route('inventory.purchases.store'), [
            'supplier_id' => $supplier->id,
            'document_type_id' => $documentType->id,
            'purchase_type' => 'contado',
            'number' => '000444',
            'purchase_date' => now()->format('Y-m-d'),
            'details' => [
                ['product_id' => $productA->id, 'quantity' => 2, 'unit_price' => 10.00],
                ['product_id' => $productB->id, 'quantity' => 3, 'unit_price' => 5.00],
            ],
        ])
        ->assertRedirect(route('inventory.purchases.index'));

    $purchase = Purchase::where('number', '000444')->first();

    $this->assertSame(35.0, (float) $purchase->total);
    $this->assertDatabaseCount('kardex_movements', 2);
});
