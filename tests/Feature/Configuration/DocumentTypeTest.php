<?php

use App\Models\Configuration\DocumentType;
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

function createDocumentTypeAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access document types index', function () {
    $this->get(route('configuration.document-types.index'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access document types index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('configuration.document-types.index'))
        ->assertForbidden();
});

test('admin can list document types', function () {
    DocumentType::factory()->create(['name' => 'Boleta']);

    $this->actingAs(createDocumentTypeAdmin())
        ->get(route('configuration.document-types.index'))
        ->assertOk()
        ->assertSee('Boleta');
});

test('admin can store a document type', function () {
    $this->actingAs(createDocumentTypeAdmin())
        ->post(route('configuration.document-types.store'), [
            'name' => 'Factura',
            'nomenclature' => 'F',
            'character_limit' => 11,
            'type' => 'invoice',
            'status' => true,
        ])
        ->assertRedirect(route('configuration.document-types.index'));

    $this->assertDatabaseHas('document_types', ['name' => 'Factura', 'type' => 'invoice']);
});

test('store document type requires a unique name', function () {
    DocumentType::factory()->create(['name' => 'Factura']);

    $this->actingAs(createDocumentTypeAdmin())
        ->post(route('configuration.document-types.store'), [
            'name' => 'Factura',
            'type' => 'invoice',
        ])
        ->assertSessionHasErrors('name');
});

test('store document type validates the type field', function () {
    $this->actingAs(createDocumentTypeAdmin())
        ->post(route('configuration.document-types.store'), [
            'name' => 'Otro',
            'type' => 'invalid',
        ])
        ->assertSessionHasErrors('type');
});

test('admin can update a document type', function () {
    $documentType = DocumentType::factory()->create(['name' => 'Antiguo']);

    $this->actingAs(createDocumentTypeAdmin())
        ->put(route('configuration.document-types.update', $documentType), [
            'name' => 'Nuevo',
            'type' => 'identification',
            'status' => false,
        ])
        ->assertRedirect(route('configuration.document-types.index'));

    $this->assertDatabaseHas('document_types', ['id' => $documentType->id, 'name' => 'Nuevo', 'status' => 0]);
});

test('admin can delete a document type without usage', function () {
    $documentType = DocumentType::factory()->create();

    $this->actingAs(createDocumentTypeAdmin())
        ->delete(route('configuration.document-types.destroy', $documentType))
        ->assertRedirect(route('configuration.document-types.index'));

    $this->assertDatabaseMissing('document_types', ['id' => $documentType->id]);
});
