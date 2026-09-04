<?php

use App\Models\Inventory\ProductCategory;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'inventario-ver']);

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['inventario-ver']);
});

function createProductCategoryAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access product categories index', function () {
    $this->get(route('inventory.product-categories.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access product categories index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('inventory.product-categories.index'))
        ->assertForbidden();
});

test('admin can list product categories', function () {
    ProductCategory::factory()->create(['name' => 'Bebidas']);

    $this->actingAs(createProductCategoryAdmin())
        ->get(route('inventory.product-categories.index'))
        ->assertOk()
        ->assertSee('Bebidas');
});

test('admin can store a product category', function () {
    $this->actingAs(createProductCategoryAdmin())
        ->post(route('inventory.product-categories.store'), [
            'name' => 'Bebidas',
            'status' => true,
        ])
        ->assertRedirect(route('inventory.product-categories.index'));

    $this->assertDatabaseHas('product_categories', ['name' => 'Bebidas']);
});

test('store product category requires a unique name', function () {
    ProductCategory::factory()->create(['name' => 'Bebidas']);

    $this->actingAs(createProductCategoryAdmin())
        ->post(route('inventory.product-categories.store'), ['name' => 'Bebidas'])
        ->assertSessionHasErrors('name');
});

test('admin can update a product category', function () {
    $productCategory = ProductCategory::factory()->create(['name' => 'Antigua']);

    $this->actingAs(createProductCategoryAdmin())
        ->put(route('inventory.product-categories.update', $productCategory), [
            'name' => 'Nueva',
            'status' => false,
        ])
        ->assertRedirect(route('inventory.product-categories.index'));

    $this->assertDatabaseHas('product_categories', ['id' => $productCategory->id, 'name' => 'Nueva', 'status' => 0]);
});

test('admin can delete a product category', function () {
    $productCategory = ProductCategory::factory()->create();

    $this->actingAs(createProductCategoryAdmin())
        ->delete(route('inventory.product-categories.destroy', $productCategory))
        ->assertRedirect(route('inventory.product-categories.index'));

    $this->assertDatabaseMissing('product_categories', ['id' => $productCategory->id]);
});
