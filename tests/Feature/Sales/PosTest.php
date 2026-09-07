<?php

use App\Models\Configuration\Company;
use App\Models\Configuration\DocumentType;
use App\Models\Configuration\PaymentMethod;
use App\Models\Configuration\SunatConfig;
use App\Models\Customers\CompanyClient;
use App\Models\Customers\Customer;
use App\Models\Inventory\Product;
use App\Models\Restaurant\CashRegisterSession;
use App\Models\Restaurant\DeliveryProvider;
use App\Models\Restaurant\Table;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'pos-ver']);
    Permission::firstOrCreate(['name' => 'pos-ventas']);
    Permission::firstOrCreate(['name' => 'pos-cobro']);
    Permission::firstOrCreate(['name' => 'pos-delivery']);
    Permission::firstOrCreate(['name' => 'pos-venta-rapida']);

    $role = Role::firstOrCreate(['name' => 'administrador']);
    $role->syncPermissions(['pos-ver', 'pos-ventas', 'pos-cobro', 'pos-delivery', 'pos-venta-rapida']);

    CashRegisterSession::factory()->create(['status' => 'open']);
});

function createPosAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrador');

    return $user;
}

test('guest cannot access the pos hall', function () {
    $this->get(route('pos.hall'))
        ->assertRedirect(route('login'));
});

test('user without permission cannot access the pos hall', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('pos.hall'))
        ->assertForbidden();
});

test('admin can see the hall with its tables', function () {
    $table = Table::factory()->create(['pos_x' => 100, 'pos_y' => 200]);

    $this->actingAs(createPosAdmin())
        ->get(route('pos.hall'))
        ->assertOk()
        ->assertSee($table->name);
});

test('opening a table creates a pending sale and marks it occupied', function () {
    $table = Table::factory()->create(['status' => 'available']);

    $this->actingAs(createPosAdmin())
        ->get(route('pos.open', $table))
        ->assertRedirect();

    $sale = Sale::where('table_id', $table->id)->where('status', 'pending')->first();

    $this->assertNotNull($sale);
    $this->assertSame('occupied', $table->fresh()->status);
});

test('opening an occupied table reuses the open sale', function () {
    $table = Table::factory()->create(['status' => 'occupied']);
    $sale = Sale::factory()->atTable($table)->create();

    $this->actingAs(createPosAdmin())
        ->get(route('pos.open', $table))
        ->assertRedirect(route('pos.sale', $sale));

    $this->assertSame(1, Sale::where('table_id', $table->id)->where('status', 'pending')->count());
});

test('adding a product updates the sale totals', function () {
    $table = Table::factory()->create();
    $sale = Sale::factory()->atTable($table)->create(['subtotal' => 0, 'total' => 0]);
    $product = Product::factory()->dish()->create(['sale_price' => 25.00]);

    $this->actingAs(createPosAdmin())
        ->from(route('pos.sale', $sale))
        ->post(route('pos.sale.add-product', $sale), [
            'product_id' => $product->id,
            'quantity' => 2,
        ])
        ->assertRedirect(route('pos.sale', $sale));

    $sale = $sale->fresh();

    $this->assertSame(50.0, (float) $sale->subtotal);
    $this->assertSame(50.0, (float) $sale->total);
    $this->assertDatabaseHas('sale_details', [
        'sale_id' => $sale->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_price' => 25.00,
        'subtotal' => 50.00,
        'kitchen_status' => 'pending',
    ]);
});

test('adding several products at once creates all details and updates totals', function () {
    $table = Table::factory()->create();
    $sale = Sale::factory()->atTable($table)->create(['subtotal' => 0, 'total' => 0]);
    $lomo = Product::factory()->dish()->create(['sale_price' => 38.00]);
    $papa = Product::factory()->dish()->create(['sale_price' => 18.00]);

    $this->actingAs(createPosAdmin())
        ->from(route('pos.sale', $sale))
        ->post(route('pos.sale.add-products', $sale), [
            'items' => [
                ['product_id' => $lomo->id, 'quantity' => 2, 'notes' => 'poco jugoso'],
                ['product_id' => $papa->id, 'quantity' => 1, 'notes' => ''],
            ],
        ])
        ->assertRedirect(route('pos.sale', $sale));

    $sale = $sale->fresh();

    $this->assertSame(94.0, (float) $sale->total);
    $this->assertDatabaseHas('sale_details', [
        'sale_id' => $sale->id,
        'product_id' => $lomo->id,
        'quantity' => 2,
        'unit_price' => 38.00,
        'subtotal' => 76.00,
        'notes' => 'poco jugoso',
        'kitchen_status' => 'pending',
    ]);
    $this->assertDatabaseHas('sale_details', [
        'sale_id' => $sale->id,
        'product_id' => $papa->id,
        'quantity' => 1,
        'unit_price' => 18.00,
        'subtotal' => 18.00,
    ]);
});

test('the bulk add requires at least one valid product', function () {
    $sale = Sale::factory()->create();

    $this->actingAs(createPosAdmin())
        ->post(route('pos.sale.add-products', $sale), [
            'items' => [],
        ])
        ->assertSessionHasErrors('items');
});

test('the pos sale page shows the bulk cart and keeps the detail steppers', function () {
    $table = Table::factory()->create();
    $sale = Sale::factory()->atTable($table)->create();
    SaleDetail::factory()->create(['sale_id' => $sale->id]);

    $this->actingAs(createPosAdmin())
        ->get(route('pos.sale', $sale))
        ->assertOk()
        ->assertSee('Agregar al pedido')
        ->assertSee('pos-cart', false)
        ->assertSee('pos-detail', false);
});

test('the pos sale page shows quantities without decimals', function () {
    $table = Table::factory()->create();
    $sale = Sale::factory()->atTable($table)->create();
    SaleDetail::factory()->create(['sale_id' => $sale->id, 'quantity' => 4]);

    $this->actingAs(createPosAdmin())
        ->get(route('pos.sale', $sale))
        ->assertOk()
        ->assertSee('value="4"', false)
        ->assertDontSee('value="4.00"', false);
});

test('the pos sale page shows product images', function () {
    $sale = Sale::factory()->create();
    $product = Product::factory()->dish()->create([
        'name' => 'Lomo Saltado',
        'image_url' => 'https://example.com/lomo.jpg',
    ]);

    $this->actingAs(createPosAdmin())
        ->get(route('pos.sale', $sale))
        ->assertOk()
        ->assertSee('https://example.com/lomo.jpg')
        ->assertSee('Lomo Saltado');
});

test('adding a product requires a valid product and quantity', function () {
    $sale = Sale::factory()->create();

    $this->actingAs(createPosAdmin())
        ->post(route('pos.sale.add-product', $sale), [
            'product_id' => 99999,
            'quantity' => 0,
        ])
        ->assertSessionHasErrors(['product_id', 'quantity']);
});

test('sending an order to the kitchen marks the sale as preparing', function () {
    $table = Table::factory()->create();
    $sale = Sale::factory()->atTable($table)->create();
    SaleDetail::factory()->create(['sale_id' => $sale->id]);

    $this->actingAs(createPosAdmin())
        ->post(route('pos.sale.kitchen', $sale))
        ->assertRedirect(route('pos.sale', $sale));

    $this->assertSame('preparing', $sale->fresh()->status);
});

test('paying a sale registers payments, client and stock kardex', function () {
    $table = Table::factory()->create(['status' => 'occupied']);
    $sale = Sale::factory()->atTable($table)->create(['subtotal' => 50, 'total' => 50, 'status' => 'preparing']);
    $supply = Product::factory()->supply()->create(['sale_price' => 25.00, 'stock' => 10]);
    SaleDetail::factory()->create([
        'sale_id' => $sale->id,
        'product_id' => $supply->id,
        'quantity' => 2,
        'unit_price' => 25.00,
        'subtotal' => 50.00,
    ]);
    $documentType = DocumentType::factory()->identification()->create();
    $paymentMethod = PaymentMethod::factory()->create();
    $customer = Customer::factory()->create();

    $this->actingAs(createPosAdmin())
        ->post(route('pos.pay', $sale), [
            'clientable_type' => 'customer',
            'clientable_id' => $customer->id,
            'document_type_id' => $documentType->id,
            'guests' => 2,
            'payments' => [
                ['payment_method_id' => $paymentMethod->id, 'amount' => 50.00],
            ],
        ])
        ->assertRedirect(route('pos.hall'));

    $sale = $sale->fresh();

    $this->assertSame('paid', $sale->status);
    $this->assertSame($customer->getMorphClass(), $sale->clientable_type);
    $this->assertSame($customer->id, $sale->clientable_id);
    $this->assertSame($documentType->id, $sale->document_type_id);

    $this->assertDatabaseHas('sale_payments', [
        'sale_id' => $sale->id,
        'payment_method_id' => $paymentMethod->id,
        'amount' => 50.00,
    ]);

    $this->assertSame(8.0, (float) $supply->fresh()->stock);

    $this->assertDatabaseHas('kardex_movements', [
        'product_id' => $supply->id,
        'movement_type' => 'sale',
        'quantity_out' => 2,
        'related_document_type' => Sale::class,
        'related_document_id' => $sale->id,
    ]);

    $this->assertSame('available', $table->fresh()->status);
});

test('paying a sale rejects a mismatched total', function () {
    $sale = Sale::factory()->create(['subtotal' => 100, 'total' => 100]);
    $paymentMethod = PaymentMethod::factory()->create();

    $this->actingAs(createPosAdmin())
        ->post(route('pos.pay', $sale), [
            'payments' => [
                ['payment_method_id' => $paymentMethod->id, 'amount' => 99.00],
            ],
        ])
        ->assertSessionHasErrors('payments');
});

test('cancelling a sale releases the table', function () {
    $table = Table::factory()->create(['status' => 'occupied']);
    $sale = Sale::factory()->atTable($table)->create();

    $this->actingAs(createPosAdmin())
        ->delete(route('pos.sale.cancel', $sale))
        ->assertRedirect(route('pos.hall'));

    $this->assertSame('cancelled', $sale->fresh()->status);
    $this->assertSame('available', $table->fresh()->status);
});

test('moving a table updates its position', function () {
    $table = Table::factory()->create(['pos_x' => 10, 'pos_y' => 10]);

    $this->actingAs(createPosAdmin())
        ->put(route('pos.tables.move', $table), [
            'pos_x' => 320,
            'pos_y' => 240,
        ])
        ->assertOk()
        ->assertJson(['ok' => true]);

    $table = $table->fresh();

    $this->assertSame(320, $table->pos_x);
    $this->assertSame(240, $table->pos_y);
});

test('the checkout page shows the guests stepper and quick amounts', function () {
    $table = Table::factory()->create();
    $sale = Sale::factory()->atTable($table)->create(['guests' => 2]);
    $documentType = DocumentType::factory()->boleta()->create();
    $paymentMethod = PaymentMethod::factory()->create();

    $this->actingAs(createPosAdmin())
        ->get(route('pos.checkout', $sale))
        ->assertOk()
        ->assertSee('data-guests-minus', false)
        ->assertSee('data-guests-plus', false)
        ->assertSee('value="2"', false)
        ->assertSee('data-quick-amount="20"', false)
        ->assertSee('data-quick-amount="50"', false)
        ->assertSee('data-quick-amount="100"', false)
        ->assertSee('data-quick-amount="200"', false)
        ->assertSee('btn.dataset.quickAmount', false)
        ->assertDontSee('btn.dataset.amount', false)
        ->assertSee($documentType->name)
        ->assertSee($paymentMethod->name);
});

test('admin can render the sale and checkout pages', function () {
    $table = Table::factory()->create();
    $sale = Sale::factory()->atTable($table)->create();
    $product = Product::factory()->dish()->create(['sale_price' => 15.00]);
    SaleDetail::factory()->create([
        'sale_id' => $sale->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
        'subtotal' => 15.00,
    ]);

    $this->actingAs(createPosAdmin())
        ->get(route('pos.sale', $sale))
        ->assertOk();

    $this->actingAs(createPosAdmin())
        ->get(route('pos.checkout', $sale))
        ->assertOk();
});

test('guest cannot create a delivery or quick-sale order', function () {
    $this->get(route('pos.new.delivery'))->assertRedirect(route('login'));
    $this->get(route('pos.new.quick-sale'))->assertRedirect(route('login'));
});

test('user without delivery permission cannot create a delivery order', function () {
    Permission::firstOrCreate(['name' => 'pos-delivery']);
    $role = Role::firstOrCreate(['name' => 'sin-delivery']);
    $role->syncPermissions(['pos-ver']);
    $user = User::factory()->create();
    $user->assignRole('sin-delivery');

    $this->actingAs($user)
        ->get(route('pos.new.delivery'))
        ->assertForbidden();
});

test('user without quick-sale permission cannot create a quick-sale order', function () {
    Permission::firstOrCreate(['name' => 'pos-venta-rapida']);
    $role = Role::firstOrCreate(['name' => 'sin-rapida']);
    $role->syncPermissions(['pos-ver']);
    $user = User::factory()->create();
    $user->assignRole('sin-rapida');

    $this->actingAs($user)
        ->get(route('pos.new.quick-sale'))
        ->assertForbidden();
});

test('creating a delivery order starts a tableless takeaway sale', function () {
    $this->actingAs(createPosAdmin())
        ->get(route('pos.new.delivery'))
        ->assertRedirect();

    $sale = Sale::firstWhere('sale_type', 'delivery');

    $this->assertNotNull($sale);
    $this->assertNull($sale->table_id);
    $this->assertTrue($sale->is_takeaway);
    $this->assertSame('pending', $sale->status);
});

test('creating a quick-sale order starts a tableless takeaway sale', function () {
    $this->actingAs(createPosAdmin())
        ->get(route('pos.new.quick-sale'))
        ->assertRedirect();

    $sale = Sale::firstWhere('sale_type', 'quick_sale');

    $this->assertNotNull($sale);
    $this->assertNull($sale->table_id);
    $this->assertTrue($sale->is_takeaway);
});

test('saving delivery info updates provider and person', function () {
    $provider = DeliveryProvider::factory()->create();
    $sale = Sale::factory()->create(['sale_type' => 'delivery']);

    $this->actingAs(createPosAdmin())
        ->patch(route('pos.sale.delivery', $sale), [
            'delivery_provider_id' => $provider->id,
            'delivery_person_name' => 'Carlos',
        ])
        ->assertRedirect();

    $sale = $sale->fresh();

    $this->assertSame($provider->id, $sale->delivery_provider_id);
    $this->assertSame('Carlos', $sale->delivery_person_name);
});

test('a delivery order can be paid without releasing any table', function () {
    $sale = Sale::factory()->create(['sale_type' => 'delivery', 'subtotal' => 50, 'total' => 50]);
    SaleDetail::factory()->create([
        'sale_id' => $sale->id,
        'product_id' => Product::factory()->supply()->create(['sale_price' => 50.00]),
        'quantity' => 1,
        'unit_price' => 50.00,
        'subtotal' => 50.00,
    ]);
    $paymentMethod = PaymentMethod::factory()->create();

    $this->actingAs(createPosAdmin())
        ->post(route('pos.pay', $sale), [
            'payments' => [
                ['payment_method_id' => $paymentMethod->id, 'amount' => 50.00],
            ],
        ])
        ->assertRedirect(route('pos.hall'));

    $this->assertSame('paid', $sale->fresh()->status);
});

test('paying an electronic invoice without a company client is rejected', function () {
    $sale = Sale::factory()->create(['subtotal' => 50, 'total' => 50]);
    $documentType = DocumentType::factory()->invoice()->create();
    $paymentMethod = PaymentMethod::factory()->create();
    $customer = Customer::factory()->create();

    $this->actingAs(createPosAdmin())
        ->post(route('pos.pay', $sale), [
            'clientable_type' => 'customer',
            'clientable_id' => $customer->id,
            'document_type_id' => $documentType->id,
            'payments' => [
                ['payment_method_id' => $paymentMethod->id, 'amount' => 50.00],
            ],
        ])
        ->assertSessionHasErrors('clientable_type');

    $this->assertSame('pending', $sale->fresh()->status);
});

test('paying an electronic invoice without an active sunat config is rejected', function () {
    $sale = Sale::factory()->create(['subtotal' => 50, 'total' => 50]);
    $documentType = DocumentType::factory()->invoice()->create();
    $paymentMethod = PaymentMethod::factory()->create();
    $company = CompanyClient::factory()->create();

    SunatConfig::factory()->create(['status' => 'inactive', 'start_date' => now()->subDays(10), 'end_date' => now()->addDays(10)]);

    $this->actingAs(createPosAdmin())
        ->post(route('pos.pay', $sale), [
            'clientable_type' => 'company',
            'clientable_id' => $company->id,
            'document_type_id' => $documentType->id,
            'payments' => [
                ['payment_method_id' => $paymentMethod->id, 'amount' => 50.00],
            ],
        ])
        ->assertSessionHasErrors('document_type_id');

    $this->assertSame('pending', $sale->fresh()->status);
});

test('paying an electronic invoice assigns series, number and increments the sunat block', function () {
    $sale = Sale::factory()->create(['subtotal' => 100, 'total' => 100]);
    $documentType = DocumentType::factory()->invoice()->create();
    $paymentMethod = PaymentMethod::factory()->create();
    $company = CompanyClient::factory()->create();
    $config = SunatConfig::factory()->create([
        'status' => 'active',
        'start_date' => now()->subDays(10),
        'end_date' => now()->addDays(10),
        'max_receipts' => 100,
        'used_receipts' => 4,
    ]);

    $this->actingAs(createPosAdmin())
        ->post(route('pos.pay', $sale), [
            'clientable_type' => 'company',
            'clientable_id' => $company->id,
            'document_type_id' => $documentType->id,
            'payments' => [
                ['payment_method_id' => $paymentMethod->id, 'amount' => 100.00],
            ],
        ])
        ->assertRedirect(route('pos.sale.receipt', $sale));

    $sale = $sale->fresh();
    $config = $config->fresh();

    $this->assertSame('paid', $sale->status);
    $this->assertSame($documentType->nomenclature.'001', $sale->series);
    $this->assertSame('00000005', $sale->number);
    $this->assertSame(5, $config->used_receipts);
});

test('a boleta is emitted without requiring a company client or ruc', function () {
    $sale = Sale::factory()->create(['subtotal' => 40, 'total' => 40]);
    $documentType = DocumentType::factory()->boleta()->create();
    $paymentMethod = PaymentMethod::factory()->create();
    SunatConfig::factory()->boleta()->create([
        'status' => 'active',
        'start_date' => now()->subDays(10),
        'end_date' => now()->addDays(10),
        'max_receipts' => 100,
        'used_receipts' => 4,
    ]);

    $this->actingAs(createPosAdmin())
        ->post(route('pos.pay', $sale), [
            'document_type_id' => $documentType->id,
            'payments' => [
                ['payment_method_id' => $paymentMethod->id, 'amount' => 40.00],
            ],
        ])
        ->assertRedirect(route('pos.sale.receipt', $sale));

    $sale = $sale->fresh();

    $this->assertSame('paid', $sale->status);
    $this->assertNull($sale->clientable_type);
    $this->assertSame('B001', $sale->series);
    $this->assertSame('00000005', $sale->number);
    $this->assertSame(5, SunatConfig::query()->latest('id')->first()->used_receipts);
});

test('emitting a boleta rejects when its block is exhausted', function () {
    $sale = Sale::factory()->create(['subtotal' => 40, 'total' => 40]);
    $documentType = DocumentType::factory()->boleta()->create();
    $paymentMethod = PaymentMethod::factory()->create();
    SunatConfig::factory()->boleta()->create([
        'status' => 'active',
        'start_date' => now()->subDays(10),
        'end_date' => now()->addDays(10),
        'max_receipts' => 2,
        'used_receipts' => 2,
    ]);

    $this->actingAs(createPosAdmin())
        ->post(route('pos.pay', $sale), [
            'document_type_id' => $documentType->id,
            'payments' => [
                ['payment_method_id' => $paymentMethod->id, 'amount' => 40.00],
            ],
        ])
        ->assertSessionHasErrors('document_type_id');

    $this->assertSame('pending', $sale->fresh()->status);
});

test('a factura cannot use a boleta block', function () {
    $sale = Sale::factory()->create(['subtotal' => 80, 'total' => 80]);
    $documentType = DocumentType::factory()->invoice()->create();
    $paymentMethod = PaymentMethod::factory()->create();
    $company = CompanyClient::factory()->create();
    SunatConfig::factory()->boleta()->create([
        'status' => 'active',
        'start_date' => now()->subDays(10),
        'end_date' => now()->addDays(10),
    ]);

    $this->actingAs(createPosAdmin())
        ->post(route('pos.pay', $sale), [
            'clientable_type' => 'company',
            'clientable_id' => $company->id,
            'document_type_id' => $documentType->id,
            'payments' => [
                ['payment_method_id' => $paymentMethod->id, 'amount' => 80.00],
            ],
        ])
        ->assertSessionHasErrors('document_type_id');

    $this->assertSame('pending', $sale->fresh()->status);
});

test('paying with more money that the total records the change', function () {
    $sale = Sale::factory()->create(['subtotal' => 45, 'total' => 45]);
    $cash = PaymentMethod::factory()->create();
    $boleta = DocumentType::factory()->boleta()->create();
    SunatConfig::factory()->boleta()->create([
        'status' => 'active',
        'start_date' => now()->subDays(10),
        'end_date' => now()->addDays(10),
    ]);

    $this->actingAs(createPosAdmin())
        ->post(route('pos.pay', $sale), [
            'document_type_id' => $boleta->id,
            'payments' => [
                ['payment_method_id' => $cash->id, 'amount' => 50.00],
            ],
        ])
        ->assertRedirect(route('pos.sale.receipt', $sale));

    $sale = $sale->fresh();

    $this->assertSame(5.0, (float) $sale->change);
    $this->assertSame('paid', $sale->status);
});

test('the receipt renders for a paid electronic invoice', function () {
    $companyClient = CompanyClient::factory()->create();
    $sale = Sale::factory()->paid()->create([
        'clientable_type' => $companyClient->getMorphClass(),
        'clientable_id' => $companyClient->id,
        'document_type_id' => DocumentType::factory()->invoice()->create()->id,
        'series' => 'F001',
        'number' => '00000001',
        'subtotal' => 100,
        'total' => 100,
    ]);
    SaleDetail::factory()->create([
        'sale_id' => $sale->id,
        'product_id' => Product::factory()->dish()->create(['sale_price' => 100.00]),
        'quantity' => 1,
        'unit_price' => 100.00,
        'subtotal' => 100.00,
    ]);

    $this->actingAs(createPosAdmin())
        ->get(route('pos.sale.receipt', $sale))
        ->assertOk()
        ->assertSee('F001-00000001');
});

test('the electronic invoice xml gains the greenter ubl document', function () {
    $company = Company::factory()->create();
    SunatConfig::factory()->create([
        'company_id' => $company->id,
        'status' => 'active',
    ]);
    $companyClient = CompanyClient::factory()->create();
    $sale = Sale::factory()->paid()->create([
        'clientable_type' => $companyClient->getMorphClass(),
        'clientable_id' => $companyClient->id,
        'document_type_id' => DocumentType::factory()->invoice()->create()->id,
        'series' => 'F001',
        'number' => '00000001',
        'subtotal' => 100,
        'total' => 100,
    ]);

    $this->actingAs(createPosAdmin())
        ->get(route('pos.sale.receipt.xml', $sale))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee('F001');
});

test('pos sales require an open cash register session', function () {
    CashRegisterSession::query()->delete();
    $table = Table::factory()->create();

    $this->actingAs(createPosAdmin())
        ->get(route('pos.open', $table))
        ->assertRedirect(route('pos.requires-session'));
});

test('creating a sale attaches the open cash register session', function () {
    $session = CashRegisterSession::where('status', 'open')->first();

    $this->actingAs(createPosAdmin())
        ->get(route('pos.new.delivery'))
        ->assertRedirect();

    $sale = Sale::firstWhere('sale_type', 'delivery');

    $this->assertSame($session->id, $sale->cash_register_session_id);
});

test('paying a sale records it in the open cash register session', function () {
    $session = CashRegisterSession::where('status', 'open')->first();
    $sale = Sale::factory()->create(['subtotal' => 50, 'total' => 50, 'cash_register_session_id' => $session->id]);
    $paymentMethod = PaymentMethod::factory()->create();

    $this->actingAs(createPosAdmin())
        ->post(route('pos.pay', $sale), [
            'payments' => [
                ['payment_method_id' => $paymentMethod->id, 'amount' => 50.00],
            ],
        ])
        ->assertRedirect(route('pos.hall'));

    $this->assertSame($session->id, $sale->fresh()->cash_register_session_id);
});
