<?php

use App\Models\Configuration\DocumentType;
use App\Models\Customers\Customer;
use App\Models\Sales\Sale;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'reportes-ver']);

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['reportes-ver']);
});

function createVentasReportAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access the ventas report', function () {
    $this->get(route('reportes.ventas'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access the ventas report', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('reportes.ventas'))
        ->assertForbidden();
});

test('admin can see paid sales from pos, delivery and quick sale', function () {
    $customer = Customer::factory()->create();

    Sale::factory()->paid()->create(['sale_type' => 'pos', 'clientable_type' => $customer->getMorphClass(), 'clientable_id' => $customer->id, 'total' => 100]);
    Sale::factory()->paid()->create(['sale_type' => 'delivery', 'total' => 50]);
    Sale::factory()->paid()->create(['sale_type' => 'quick_sale', 'total' => 30]);

    $this->actingAs(createVentasReportAdmin())
        ->get(route('reportes.ventas'))
        ->assertOk()
        ->assertSee('Delivery')
        ->assertSee('Venta rápida')
        ->assertSee('180.00');
});

test('a pending sale is not included in the ventas report totals', function () {
    $sale = Sale::factory()->create(['sale_type' => 'pos', 'status' => 'pending', 'total' => 999]);

    $this->actingAs(createVentasReportAdmin())
        ->get(route('reportes.ventas'))
        ->assertOk()
        ->assertDontSee('999.00');
});

test('the ventas report can be filtered by sale type', function () {
    Sale::factory()->paid()->create(['sale_type' => 'pos', 'total' => 100]);
    Sale::factory()->paid()->create(['sale_type' => 'delivery', 'total' => 50]);

    $this->actingAs(createVentasReportAdmin())
        ->get(route('reportes.ventas', ['sale_type' => 'delivery']))
        ->assertOk();
});

test('the ventas report only offers boleta and factura as document filter', function () {
    $boleta = DocumentType::factory()->boleta()->create();
    $factura = DocumentType::factory()->invoice()->create();
    $dni = DocumentType::factory()->identification()->create();

    $this->actingAs(createVentasReportAdmin())
        ->get(route('reportes.ventas'))
        ->assertOk()
        ->assertSee('value="'.$boleta->id.'"', false)
        ->assertSee('value="'.$factura->id.'"', false)
        ->assertDontSee('value="'.$dni->id.'"', false)
        ->assertSee('Boleta')
        ->assertSee('Factura');
});

test('the ventas report can preview the pdf inline', function () {
    Sale::factory()->paid()->create(['sale_type' => 'pos', 'total' => 100]);

    $response = $this->actingAs(createVentasReportAdmin())
        ->get(route('reportes.ventas.preview'));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeaderMissing('Content-Disposition');
});

test('the ventas report exports a pdf attachment with a dated filename', function () {
    Sale::factory()->paid()->create(['sale_type' => 'pos', 'total' => 100]);

    $response = $this->actingAs(createVentasReportAdmin())
        ->get(route('reportes.ventas.export'));

    $disposition = $response->headers->get('Content-Disposition');

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');

    expect($disposition)->toStartWith('attachment; filename="reporte_ventas_')
        ->and($disposition)->toEndWith('.pdf"')
        ->and($disposition)->toMatch('/reporte_ventas_\d{4}-\d{2}-\d{2}_\d{6}\.pdf/');
});
