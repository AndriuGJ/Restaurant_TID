<?php

use App\Models\Inventory\PurchaseCategory;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'inventario-ver']);

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['inventario-ver']);
});

function createPurchaseCategoryAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access purchase categories index', function () {
    $this->get(route('inventory.purchase-categories.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access purchase categories index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('inventory.purchase-categories.index'))
        ->assertForbidden();
});

test('admin can list purchase categories', function () {
    PurchaseCategory::factory()->create(['name' => 'Empaque']);

    $this->actingAs(createPurchaseCategoryAdmin())
        ->get(route('inventory.purchase-categories.index'))
        ->assertOk()
        ->assertSee('Empaque');
});

test('admin can store a purchase category', function () {
    $this->actingAs(createPurchaseCategoryAdmin())
        ->post(route('inventory.purchase-categories.store'), [
            'name' => 'Materia Prima',
        ])
        ->assertRedirect(route('inventory.purchase-categories.index'));

    $this->assertDatabaseHas('purchase_categories', ['name' => 'Materia Prima']);
});

test('store purchase category requires a unique name', function () {
    PurchaseCategory::factory()->create(['name' => 'Materia Prima']);

    $this->actingAs(createPurchaseCategoryAdmin())
        ->post(route('inventory.purchase-categories.store'), ['name' => 'Materia Prima'])
        ->assertSessionHasErrors('name');
});

test('admin can update a purchase category', function () {
    $purchaseCategory = PurchaseCategory::factory()->create(['name' => 'Antigua']);

    $this->actingAs(createPurchaseCategoryAdmin())
        ->put(route('inventory.purchase-categories.update', $purchaseCategory), ['name' => 'Nueva'])
        ->assertRedirect(route('inventory.purchase-categories.index'));

    $this->assertDatabaseHas('purchase_categories', ['id' => $purchaseCategory->id, 'name' => 'Nueva']);
});

test('admin can delete a purchase category', function () {
    $purchaseCategory = PurchaseCategory::factory()->create();

    $this->actingAs(createPurchaseCategoryAdmin())
        ->delete(route('inventory.purchase-categories.destroy', $purchaseCategory))
        ->assertRedirect(route('inventory.purchase-categories.index'));

    $this->assertDatabaseMissing('purchase_categories', ['id' => $purchaseCategory->id]);
});
