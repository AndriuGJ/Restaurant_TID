<?php

use App\Models\Inventory\Product;
use App\Models\Inventory\ProductCategory;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'inventario-ver']);
    Permission::firstOrCreate(['name' => 'productos-gestionar']);

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['inventario-ver', 'productos-gestionar']);
});

function createProductAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access products index', function () {
    $this->get(route('inventory.products.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access products index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('inventory.products.index'))
        ->assertForbidden();
});

test('admin can list products', function () {
    Product::factory()->supply()->create(['name' => 'Papa amarilla']);

    $this->actingAs(createProductAdmin())
        ->get(route('inventory.products.index'))
        ->assertOk()
        ->assertSee('Papa amarilla');
});

test('admin can store a product', function () {
    $category = ProductCategory::factory()->create();

    $this->actingAs(createProductAdmin())
        ->post(route('inventory.products.store'), [
            'name' => 'Ceviche mixto',
            'type' => 'dish',
            'product_category_id' => $category->id,
            'sale_price' => 45.00,
            'stock' => 0,
            'status' => true,
        ])
        ->assertRedirect(route('inventory.products.index'));

    $this->assertDatabaseHas('products', ['name' => 'Ceviche mixto', 'type' => 'dish']);
});

test('admin can store a supply', function () {
    $this->actingAs(createProductAdmin())
        ->post(route('inventory.products.store'), [
            'name' => 'Papa amarilla',
            'type' => 'supply',
            'stock' => 100,
            'status' => true,
        ])
        ->assertRedirect(route('inventory.products.index'));

    $this->assertDatabaseHas('products', ['name' => 'Papa amarilla', 'type' => 'supply']);
});

test('store product requires a valid type', function () {
    $this->actingAs(createProductAdmin())
        ->post(route('inventory.products.store'), [
            'name' => 'Inválido',
            'type' => 'bebida',
        ])
        ->assertSessionHasErrors('type');
});

test('admin can update a product', function () {
    $product = Product::factory()->supply()->create(['name' => 'Antiguo']);

    $this->actingAs(createProductAdmin())
        ->put(route('inventory.products.update', $product), [
            'name' => 'Nuevo',
            'type' => 'supply',
            'stock' => 50,
            'status' => true,
        ])
        ->assertRedirect(route('inventory.products.index'));

    $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Nuevo']);
});

test('admin can delete a product without purchases or sales', function () {
    $product = Product::factory()->supply()->create();

    $this->actingAs(createProductAdmin())
        ->delete(route('inventory.products.destroy', $product))
        ->assertRedirect(route('inventory.products.index'));

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

test('store dish syncs its ingredients', function () {
    $supply = Product::factory()->supply()->create();

    $this->actingAs(createProductAdmin())
        ->post(route('inventory.products.store'), [
            'name' => 'Lomo saltado',
            'type' => 'dish',
            'ingredients' => [
                ['ingredient_id' => $supply->id, 'quantity' => 2],
            ],
            'status' => true,
        ])
        ->assertRedirect(route('inventory.products.index'));

    $product = Product::where('name', 'Lomo saltado')->first();

    $this->assertDatabaseHas('product_ingredients', [
        'dish_id' => $product->id,
        'ingredient_id' => $supply->id,
        'quantity' => 2,
    ]);
});

test('update dish replaces its ingredients', function () {
    $product = Product::factory()->dish()->create();
    $oldIngredient = $product->ingredients()->first();
    $newSupply = Product::factory()->supply()->create();

    $this->actingAs(createProductAdmin())
        ->put(route('inventory.products.update', $product), [
            'name' => $product->name,
            'type' => 'dish',
            'ingredients' => [
                ['ingredient_id' => $newSupply->id, 'quantity' => 3],
            ],
            'status' => true,
        ])
        ->assertRedirect(route('inventory.products.index'));

    $this->assertDatabaseMissing('product_ingredients', ['dish_id' => $product->id, 'ingredient_id' => $oldIngredient->id]);
    $this->assertDatabaseHas('product_ingredients', [
        'dish_id' => $product->id,
        'ingredient_id' => $newSupply->id,
        'quantity' => 3,
    ]);
});

test('a store with an invalid ingredient is rejected', function () {
    $this->actingAs(createProductAdmin())
        ->post(route('inventory.products.store'), [
            'name' => 'Plato inválido',
            'type' => 'dish',
            'ingredients' => [
                ['ingredient_id' => 9999, 'quantity' => 1],
            ],
            'status' => true,
        ])
        ->assertSessionHasErrors('ingredients.0.ingredient_id');
});
