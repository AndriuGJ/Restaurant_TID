<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CashRegisters\CashRegisterSessionController;
use App\Http\Controllers\Configuration\CompanyController;
use App\Http\Controllers\Configuration\DocumentTypeController;
use App\Http\Controllers\Configuration\PaymentMethodController;
use App\Http\Controllers\Configuration\SunatConfigController;
use App\Http\Controllers\Customers\CompanyClientController;
use App\Http\Controllers\Customers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Inventory\KardexController;
use App\Http\Controllers\Inventory\ProductCategoryController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\PurchaseCategoryController;
use App\Http\Controllers\Inventory\PurchaseController;
use App\Http\Controllers\Inventory\SupplierController;
use App\Http\Controllers\Reports\CajasReportController;
use App\Http\Controllers\Reports\VentasReportController;
use App\Http\Controllers\Restaurant\CashRegisterController;
use App\Http\Controllers\Restaurant\DeliveryProviderController;
use App\Http\Controllers\Restaurant\HallController;
use App\Http\Controllers\Restaurant\PrinterController;
use App\Http\Controllers\Restaurant\ShiftController;
use App\Http\Controllers\Restaurant\TableController;
use App\Http\Controllers\Sales\KitchenController;
use App\Http\Controllers\Sales\PosController;
use App\Http\Controllers\Users\RoleController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Configuración → Restaurante: Salones
    Route::prefix('restaurante')->name('restaurant.')->group(function () {
        Route::get('halls', [HallController::class, 'index'])
            ->middleware('permission:configuracion-ver')
            ->name('halls.index');

        Route::get('halls/create', [HallController::class, 'create'])
            ->middleware('permission:configuracion-editar')
            ->name('halls.create');

        Route::post('halls', [HallController::class, 'store'])
            ->middleware('permission:configuracion-editar')
            ->name('halls.store');

        Route::get('halls/{hall}/edit', [HallController::class, 'edit'])
            ->middleware('permission:configuracion-editar')
            ->name('halls.edit');

        Route::put('halls/{hall}', [HallController::class, 'update'])
            ->middleware('permission:configuracion-editar')
            ->name('halls.update');

        Route::delete('halls/{hall}', [HallController::class, 'destroy'])
            ->middleware('permission:configuracion-editar')
            ->name('halls.destroy');

        Route::get('tables', [TableController::class, 'index'])
            ->middleware('permission:configuracion-ver')
            ->name('tables.index');

        Route::get('tables/create', [TableController::class, 'create'])
            ->middleware('permission:configuracion-editar')
            ->name('tables.create');

        Route::post('tables', [TableController::class, 'store'])
            ->middleware('permission:configuracion-editar')
            ->name('tables.store');

        Route::get('tables/{table}/edit', [TableController::class, 'edit'])
            ->middleware('permission:configuracion-editar')
            ->name('tables.edit');

        Route::put('tables/{table}', [TableController::class, 'update'])
            ->middleware('permission:configuracion-editar')
            ->name('tables.update');

        Route::delete('tables/{table}', [TableController::class, 'destroy'])
            ->middleware('permission:configuracion-editar')
            ->name('tables.destroy');

        Route::get('shifts', [ShiftController::class, 'index'])
            ->middleware('permission:configuracion-ver')
            ->name('shifts.index');

        Route::get('shifts/create', [ShiftController::class, 'create'])
            ->middleware('permission:configuracion-editar')
            ->name('shifts.create');

        Route::post('shifts', [ShiftController::class, 'store'])
            ->middleware('permission:configuracion-editar')
            ->name('shifts.store');

        Route::get('shifts/{shift}/edit', [ShiftController::class, 'edit'])
            ->middleware('permission:configuracion-editar')
            ->name('shifts.edit');

        Route::put('shifts/{shift}', [ShiftController::class, 'update'])
            ->middleware('permission:configuracion-editar')
            ->name('shifts.update');

        Route::delete('shifts/{shift}', [ShiftController::class, 'destroy'])
            ->middleware('permission:configuracion-editar')
            ->name('shifts.destroy');

        Route::get('cash-registers', [CashRegisterController::class, 'index'])
            ->middleware('permission:configuracion-ver')
            ->name('cash-registers.index');

        Route::get('cash-registers/create', [CashRegisterController::class, 'create'])
            ->middleware('permission:configuracion-editar')
            ->name('cash-registers.create');

        Route::post('cash-registers', [CashRegisterController::class, 'store'])
            ->middleware('permission:configuracion-editar')
            ->name('cash-registers.store');

        Route::get('cash-registers/{cashRegister}/edit', [CashRegisterController::class, 'edit'])
            ->middleware('permission:configuracion-editar')
            ->name('cash-registers.edit');

        Route::put('cash-registers/{cashRegister}', [CashRegisterController::class, 'update'])
            ->middleware('permission:configuracion-editar')
            ->name('cash-registers.update');

        Route::delete('cash-registers/{cashRegister}', [CashRegisterController::class, 'destroy'])
            ->middleware('permission:configuracion-editar')
            ->name('cash-registers.destroy');

        Route::get('delivery-providers', [DeliveryProviderController::class, 'index'])
            ->middleware('permission:configuracion-ver')
            ->name('delivery-providers.index');

        Route::get('delivery-providers/create', [DeliveryProviderController::class, 'create'])
            ->middleware('permission:configuracion-editar')
            ->name('delivery-providers.create');

        Route::post('delivery-providers', [DeliveryProviderController::class, 'store'])
            ->middleware('permission:configuracion-editar')
            ->name('delivery-providers.store');

        Route::get('delivery-providers/{deliveryProvider}/edit', [DeliveryProviderController::class, 'edit'])
            ->middleware('permission:configuracion-editar')
            ->name('delivery-providers.edit');

        Route::put('delivery-providers/{deliveryProvider}', [DeliveryProviderController::class, 'update'])
            ->middleware('permission:configuracion-editar')
            ->name('delivery-providers.update');

        Route::delete('delivery-providers/{deliveryProvider}', [DeliveryProviderController::class, 'destroy'])
            ->middleware('permission:configuracion-editar')
            ->name('delivery-providers.destroy');

        Route::get('printers', [PrinterController::class, 'index'])
            ->middleware('permission:configuracion-ver')
            ->name('printers.index');

        Route::get('printers/create', [PrinterController::class, 'create'])
            ->middleware('permission:configuracion-editar')
            ->name('printers.create');

        Route::post('printers', [PrinterController::class, 'store'])
            ->middleware('permission:configuracion-editar')
            ->name('printers.store');

        Route::get('printers/{printer}/edit', [PrinterController::class, 'edit'])
            ->middleware('permission:configuracion-editar')
            ->name('printers.edit');

        Route::put('printers/{printer}', [PrinterController::class, 'update'])
            ->middleware('permission:configuracion-editar')
            ->name('printers.update');

        Route::delete('printers/{printer}', [PrinterController::class, 'destroy'])
            ->middleware('permission:configuracion-editar')
            ->name('printers.destroy');

        Route::post('printers/{printer}/probar', [PrinterController::class, 'test'])
            ->middleware('permission:configuracion-editar')
            ->name('printers.test');

        Route::post('printers/escanear', [PrinterController::class, 'scan'])
            ->middleware('permission:configuracion-editar')
            ->name('printers.scan');

        Route::post('printers/colas', [PrinterController::class, 'queues'])
            ->middleware('permission:configuracion-editar')
            ->name('printers.queues');
    });

    // Configuración → Sistema
    Route::prefix('configuracion')->name('configuration.')->group(function () {
        Route::get('document-types', [DocumentTypeController::class, 'index'])
            ->middleware('permission:configuracion-ver')
            ->name('document-types.index');

        Route::get('document-types/create', [DocumentTypeController::class, 'create'])
            ->middleware('permission:configuracion-editar')
            ->name('document-types.create');

        Route::post('document-types', [DocumentTypeController::class, 'store'])
            ->middleware('permission:configuracion-editar')
            ->name('document-types.store');

        Route::get('document-types/{documentType}/edit', [DocumentTypeController::class, 'edit'])
            ->middleware('permission:configuracion-editar')
            ->name('document-types.edit');

        Route::put('document-types/{documentType}', [DocumentTypeController::class, 'update'])
            ->middleware('permission:configuracion-editar')
            ->name('document-types.update');

        Route::delete('document-types/{documentType}', [DocumentTypeController::class, 'destroy'])
            ->middleware('permission:configuracion-editar')
            ->name('document-types.destroy');

        Route::get('payment-methods', [PaymentMethodController::class, 'index'])
            ->middleware('permission:configuracion-ver')
            ->name('payment-methods.index');

        Route::get('payment-methods/create', [PaymentMethodController::class, 'create'])
            ->middleware('permission:configuracion-editar')
            ->name('payment-methods.create');

        Route::post('payment-methods', [PaymentMethodController::class, 'store'])
            ->middleware('permission:configuracion-editar')
            ->name('payment-methods.store');

        Route::get('payment-methods/{paymentMethod}/edit', [PaymentMethodController::class, 'edit'])
            ->middleware('permission:configuracion-editar')
            ->name('payment-methods.edit');

        Route::put('payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])
            ->middleware('permission:configuracion-editar')
            ->name('payment-methods.update');

        Route::delete('payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy'])
            ->middleware('permission:configuracion-editar')
            ->name('payment-methods.destroy');

        Route::get('companies', [CompanyController::class, 'index'])
            ->middleware('permission:configuracion-ver')
            ->name('companies.index');

        Route::get('companies/{company}/edit', [CompanyController::class, 'edit'])
            ->middleware('permission:configuracion-editar')
            ->name('companies.edit');

        Route::put('companies/{company}', [CompanyController::class, 'update'])
            ->middleware('permission:configuracion-editar')
            ->name('companies.update');

        Route::get('sunat-configs', [SunatConfigController::class, 'index'])
            ->middleware('permission:configuracion-ver')
            ->name('sunat-configs.index');

        Route::get('sunat-configs/create', [SunatConfigController::class, 'create'])
            ->middleware('permission:configuracion-editar')
            ->name('sunat-configs.create');

        Route::post('sunat-configs', [SunatConfigController::class, 'store'])
            ->middleware('permission:configuracion-editar')
            ->name('sunat-configs.store');

        Route::get('sunat-configs/{sunatConfig}/edit', [SunatConfigController::class, 'edit'])
            ->middleware('permission:configuracion-editar')
            ->name('sunat-configs.edit');

        Route::put('sunat-configs/{sunatConfig}', [SunatConfigController::class, 'update'])
            ->middleware('permission:configuracion-editar')
            ->name('sunat-configs.update');

        Route::delete('sunat-configs/{sunatConfig}', [SunatConfigController::class, 'destroy'])
            ->middleware('permission:configuracion-editar')
            ->name('sunat-configs.destroy');
    });

    // Cajas: apertura/cierre de sesiones
    Route::prefix('cajas')->name('cash-registers.sessions.')->group(function () {
        Route::get('', [CashRegisterSessionController::class, 'index'])
            ->middleware('permission:cajas-ver')
            ->name('index');

        Route::get('abrir', [CashRegisterSessionController::class, 'create'])
            ->middleware('permission:cajas-abrir')
            ->name('create');

        Route::post('', [CashRegisterSessionController::class, 'store'])
            ->middleware('permission:cajas-abrir')
            ->name('store');

        Route::get('{session}/cerrar', [CashRegisterSessionController::class, 'edit'])
            ->middleware('permission:cajas-cerrar')
            ->name('edit');

        Route::put('{session}', [CashRegisterSessionController::class, 'update'])
            ->middleware('permission:cajas-cerrar')
            ->name('update');
    });

    // Clientes
    Route::prefix('clientes')->name('customers.')->group(function () {
        Route::get('', [CustomerController::class, 'index'])
            ->middleware('permission:clientes-ver')
            ->name('index');

        Route::get('create', [CustomerController::class, 'create'])
            ->middleware('permission:clientes-gestionar')
            ->name('create');

        Route::post('', [CustomerController::class, 'store'])
            ->middleware('permission:clientes-gestionar')
            ->name('store');

        Route::get('{customer}/edit', [CustomerController::class, 'edit'])
            ->middleware('permission:clientes-gestionar')
            ->name('edit');

        Route::put('{customer}', [CustomerController::class, 'update'])
            ->middleware('permission:clientes-gestionar')
            ->name('update');

        Route::delete('{customer}', [CustomerController::class, 'destroy'])
            ->middleware('permission:clientes-gestionar')
            ->name('destroy');

        Route::get('empresas', [CompanyClientController::class, 'index'])
            ->middleware('permission:clientes-ver')
            ->name('companies.index');

        Route::get('empresas/create', [CompanyClientController::class, 'create'])
            ->middleware('permission:clientes-gestionar')
            ->name('companies.create');

        Route::post('empresas', [CompanyClientController::class, 'store'])
            ->middleware('permission:clientes-gestionar')
            ->name('companies.store');

        Route::get('empresas/{companyClient}/edit', [CompanyClientController::class, 'edit'])
            ->middleware('permission:clientes-gestionar')
            ->name('companies.edit');

        Route::put('empresas/{companyClient}', [CompanyClientController::class, 'update'])
            ->middleware('permission:clientes-gestionar')
            ->name('companies.update');

        Route::delete('empresas/{companyClient}', [CompanyClientController::class, 'destroy'])
            ->middleware('permission:clientes-gestionar')
            ->name('companies.destroy');
    });

    // Inventario
    Route::prefix('inventario')->name('inventory.')->group(function () {
        Route::get('proveedores', [SupplierController::class, 'index'])
            ->middleware('permission:inventario-ver')
            ->name('suppliers.index');

        Route::get('proveedores/create', [SupplierController::class, 'create'])
            ->middleware('permission:inventario-ver')
            ->name('suppliers.create');

        Route::post('proveedores', [SupplierController::class, 'store'])
            ->middleware('permission:inventario-ver')
            ->name('suppliers.store');

        Route::get('proveedores/{supplier}/edit', [SupplierController::class, 'edit'])
            ->middleware('permission:inventario-ver')
            ->name('suppliers.edit');

        Route::put('proveedores/{supplier}', [SupplierController::class, 'update'])
            ->middleware('permission:inventario-ver')
            ->name('suppliers.update');

        Route::delete('proveedores/{supplier}', [SupplierController::class, 'destroy'])
            ->middleware('permission:inventario-ver')
            ->name('suppliers.destroy');

        Route::get('categorias-compra', [PurchaseCategoryController::class, 'index'])
            ->middleware('permission:inventario-ver')
            ->name('purchase-categories.index');

        Route::get('categorias-compra/create', [PurchaseCategoryController::class, 'create'])
            ->middleware('permission:inventario-ver')
            ->name('purchase-categories.create');

        Route::post('categorias-compra', [PurchaseCategoryController::class, 'store'])
            ->middleware('permission:inventario-ver')
            ->name('purchase-categories.store');

        Route::get('categorias-compra/{purchaseCategory}/edit', [PurchaseCategoryController::class, 'edit'])
            ->middleware('permission:inventario-ver')
            ->name('purchase-categories.edit');

        Route::put('categorias-compra/{purchaseCategory}', [PurchaseCategoryController::class, 'update'])
            ->middleware('permission:inventario-ver')
            ->name('purchase-categories.update');

        Route::delete('categorias-compra/{purchaseCategory}', [PurchaseCategoryController::class, 'destroy'])
            ->middleware('permission:inventario-ver')
            ->name('purchase-categories.destroy');

        Route::get('categorias-producto', [ProductCategoryController::class, 'index'])
            ->middleware('permission:inventario-ver')
            ->name('product-categories.index');

        Route::get('categorias-producto/create', [ProductCategoryController::class, 'create'])
            ->middleware('permission:inventario-ver')
            ->name('product-categories.create');

        Route::post('categorias-producto', [ProductCategoryController::class, 'store'])
            ->middleware('permission:inventario-ver')
            ->name('product-categories.store');

        Route::get('categorias-producto/{productCategory}/edit', [ProductCategoryController::class, 'edit'])
            ->middleware('permission:inventario-ver')
            ->name('product-categories.edit');

        Route::put('categorias-producto/{productCategory}', [ProductCategoryController::class, 'update'])
            ->middleware('permission:inventario-ver')
            ->name('product-categories.update');

        Route::delete('categorias-producto/{productCategory}', [ProductCategoryController::class, 'destroy'])
            ->middleware('permission:inventario-ver')
            ->name('product-categories.destroy');

        Route::get('productos', [ProductController::class, 'index'])
            ->middleware('permission:inventario-ver')
            ->name('products.index');

        Route::get('productos/create', [ProductController::class, 'create'])
            ->middleware('permission:productos-gestionar')
            ->name('products.create');

        Route::post('productos', [ProductController::class, 'store'])
            ->middleware('permission:productos-gestionar')
            ->name('products.store');

        Route::get('productos/{product}/edit', [ProductController::class, 'edit'])
            ->middleware('permission:productos-gestionar')
            ->name('products.edit');

        Route::put('productos/{product}', [ProductController::class, 'update'])
            ->middleware('permission:productos-gestionar')
            ->name('products.update');

        Route::delete('productos/{product}', [ProductController::class, 'destroy'])
            ->middleware('permission:productos-gestionar')
            ->name('products.destroy');

        Route::get('compras', [PurchaseController::class, 'index'])
            ->middleware('permission:inventario-ver')
            ->name('purchases.index');

        Route::get('compras/create', [PurchaseController::class, 'create'])
            ->middleware('permission:compras-gestionar')
            ->name('purchases.create');

        Route::post('compras', [PurchaseController::class, 'store'])
            ->middleware('permission:compras-gestionar')
            ->name('purchases.store');

        Route::get('compras/{purchase}', [PurchaseController::class, 'show'])
            ->middleware('permission:inventario-ver')
            ->name('purchases.show');

        Route::get('kardex', [KardexController::class, 'index'])
            ->middleware('permission:kardex-ver')
            ->name('kardex.index');

        Route::get('kardex/exportar/pdf', [KardexController::class, 'exportPdf'])
            ->middleware('permission:kardex-ver')
            ->name('kardex.export-pdf');

        Route::get('kardex/exportar/excel', [KardexController::class, 'exportExcel'])
            ->middleware('permission:kardex-ver')
            ->name('kardex.export-excel');
    });

    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'hall'])
            ->middleware('permission:pos-ver')
            ->name('hall');

        Route::put('mesas/{table}/mover', [PosController::class, 'moveTable'])
            ->middleware('permission:pos-ver')
            ->name('tables.move');

        Route::post('mesas/{table}/reservas', [PosController::class, 'reserve'])
            ->middleware('permission:pos-ventas')
            ->name('tables.reserve');

        Route::delete('mesas/{table}/reservas', [PosController::class, 'cancelReservation'])
            ->middleware('permission:pos-ventas')
            ->name('tables.reservations.cancel');

        Route::get('requiere-caja', [PosController::class, 'requiresSession'])
            ->middleware('permission:pos-ver')
            ->name('requires-session');

        Route::get('mesas/{table}/abrir', [PosController::class, 'openSale'])
            ->middleware(['permission:pos-ventas', 'ensure.pos.session'])
            ->name('open');

        Route::get('nueva-venta/delivery', [PosController::class, 'newDelivery'])
            ->middleware(['permission:pos-delivery', 'ensure.pos.session'])
            ->name('new.delivery');

        Route::get('nueva-venta/rapida', [PosController::class, 'newQuickSale'])
            ->middleware(['permission:pos-venta-rapida', 'ensure.pos.session'])
            ->name('new.quick-sale');

        Route::patch('venta/{sale}/delivery', [PosController::class, 'saveDeliveryInfo'])
            ->middleware(['permission:pos-delivery', 'ensure.pos.session'])
            ->name('sale.delivery');

        Route::get('venta/{sale}', [PosController::class, 'sale'])
            ->middleware(['permission:pos-ventas', 'ensure.pos.session'])
            ->name('sale');

        Route::post('venta/{sale}/producto', [PosController::class, 'addProduct'])
            ->middleware(['permission:pos-ventas', 'ensure.pos.session'])
            ->name('sale.add-product');

        Route::post('venta/{sale}/productos/agregar', [PosController::class, 'bulkAddProducts'])
            ->middleware(['permission:pos-ventas', 'ensure.pos.session'])
            ->name('sale.add-products');

        Route::patch('detalle/{detail}', [PosController::class, 'updateDetailQuantity'])
            ->middleware(['permission:pos-ventas', 'ensure.pos.session'])
            ->name('sale.detail.update');

        Route::delete('detalle/{detail}', [PosController::class, 'removeDetail'])
            ->middleware(['permission:pos-ventas', 'ensure.pos.session'])
            ->name('sale.detail.remove');

        Route::post('venta/{sale}/cocina', [PosController::class, 'sendToKitchen'])
            ->middleware(['permission:pos-ventas', 'ensure.pos.session'])
            ->name('sale.kitchen');

        Route::get('venta/{sale}/cobro', [PosController::class, 'checkout'])
            ->middleware(['permission:pos-cobro', 'ensure.pos.session'])
            ->name('checkout');

        Route::get('venta/{sale}/precuenta', [PosController::class, 'precuenta'])
            ->middleware(['permission:pos-ventas', 'ensure.pos.session'])
            ->name('sale.precuenta');

        Route::post('venta/{sale}/pagar', [PosController::class, 'pay'])
            ->middleware(['permission:pos-cobro', 'ensure.pos.session'])
            ->name('pay');

        Route::get('venta/{sale}/comprobante', [PosController::class, 'receipt'])
            ->middleware(['permission:pos-cobro', 'ensure.pos.session'])
            ->name('sale.receipt');

        Route::post('venta/{sale}/comprobante/imprimir', [PosController::class, 'printReceipt'])
            ->middleware(['permission:pos-cobro', 'ensure.pos.session'])
            ->name('sale.receipt.print');

        Route::get('venta/{sale}/comprobante/xml', [PosController::class, 'receiptXml'])
            ->middleware(['permission:pos-cobro', 'ensure.pos.session'])
            ->name('sale.receipt.xml');

        Route::delete('venta/{sale}', [PosController::class, 'cancel'])
            ->middleware(['permission:pos-ventas', 'ensure.pos.session'])
            ->name('sale.cancel');

        Route::get('cocina', [KitchenController::class, 'index'])
            ->middleware('permission:pos-preparacion')
            ->name('kitchen.index');

        Route::post('cocina/detalle/{detail}/iniciar', [KitchenController::class, 'start'])
            ->middleware('permission:pos-preparacion')
            ->name('kitchen.start');

        Route::post('cocina/detalle/{detail}/completar', [KitchenController::class, 'complete'])
            ->middleware('permission:pos-preparacion')
            ->name('kitchen.complete');
    });

    // Reportes
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('ventas', [VentasReportController::class, 'index'])
            ->middleware('permission:reportes-ver')
            ->name('ventas');

        Route::get('ventas/preview', [VentasReportController::class, 'preview'])
            ->middleware('permission:reportes-ver')
            ->name('ventas.preview');

        Route::get('ventas/exportar', [VentasReportController::class, 'export'])
            ->middleware('permission:reportes-ver')
            ->name('ventas.export');

        Route::get('cajas', [CajasReportController::class, 'index'])
            ->middleware('permission:reportes-cajas-ver')
            ->name('cajas');
    });

    // Usuarios
    Route::prefix('usuarios')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:usuarios-ver')
            ->name('index');

        Route::get('crear', [UserController::class, 'create'])
            ->middleware('permission:usuarios-gestionar')
            ->name('create');

        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:usuarios-gestionar')
            ->name('store');

        Route::get('{user}/editar', [UserController::class, 'edit'])
            ->middleware('permission:usuarios-gestionar')
            ->name('edit');

        Route::put('{user}', [UserController::class, 'update'])
            ->middleware('permission:usuarios-gestionar')
            ->name('update');

        Route::delete('{user}', [UserController::class, 'destroy'])
            ->middleware('permission:usuarios-gestionar')
            ->name('destroy');

        Route::get('roles', [RoleController::class, 'index'])
            ->middleware('permission:roles-gestionar')
            ->name('roles.index');

        Route::get('roles/crear', [RoleController::class, 'create'])
            ->middleware('permission:roles-gestionar')
            ->name('roles.create');

        Route::post('roles', [RoleController::class, 'store'])
            ->middleware('permission:roles-gestionar')
            ->name('roles.store');

        Route::get('roles/{role}/editar', [RoleController::class, 'edit'])
            ->middleware('permission:roles-gestionar')
            ->name('roles.edit');

        Route::put('roles/{role}', [RoleController::class, 'update'])
            ->middleware('permission:roles-gestionar')
            ->name('roles.update');

        Route::delete('roles/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:roles-gestionar')
            ->name('roles.destroy');
    });
});
