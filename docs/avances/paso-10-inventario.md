# Paso 10 — Módulo Inventario (Fases 1, 2 y 3)

Fecha: 2026-09-02

## Objetivo
Implementar el módulo **Inventario** según la sección 7.4 del README, en tres fases: Fase 1 (catálogos base: Proveedores, Categorías de compra/producto), Fase 2 (**Productos**: platos, insumos y combos con sus ingredientes) y Fase 3 (**Compras + Kardex**: registro de compras con entrada de stock y generación automática de movimientos de kardex). Con esto el módulo Inventario queda **completo**.

## Qué se hizo

### 1. Form Requests (`app/Http/Requests/Inventory/`)
Store/Update para cada uno de los tres catálogos:
- `Supplier`: `ruc` requerido y único (con `ignore` en update), `social_reason` requerido, `phone`/`contact_person`/`email` opcionales y `status` booleano.
- `PurchaseCategory`: `name` requerido y único, `description` opcional.
- `ProductCategory`: `name` requerido y único, `description` opcional, `status` booleano.

Todos `authorize()` con `inventario-ver`.

### 2. Rutas (`routes/web.php`)
Subgrupo `Route::prefix('inventario')->name('inventory.')`:
- `/proveedores` → `suppliers.*`, `/categorias-compra` → `purchase-categories.*`, `/categorias-producto` → `product-categories.*`: `index/create/store/{id}/edit/update/destroy`, todas con middleware `permission:inventario-ver`.
- Stubs de Fases futuras: `/productos` → `products.index`, `/compras` → `purchases.index`, `/kardex` → `kardex.index` (con `permission:inventario-ver`).

### 3. Vistas (`resources/views/inventory/`)
- `suppliers/`, `purchase-categories/`, `product-categories/` con `index`, `create`, `edit`, `_form` siguiendo el patrón establecido.
- `products/`, `purchases/`, `kardex/` con `index` placeholder ("Módulo en construcción") para que el sidebar funcione.
- El partial `_partials/row-actions.blade.php` ahora acepta `$permission` (default `configuracion-editar`) y se pasa `inventario-ver` desde estas vistas de inventario.

### 4. Sidebar (`resources/views/_partials/sidebar.blade.php`)
Nueva sección **"Inventario"** (permiso `inventario-ver`) con grupos Catálogo (Proveedores, Categorías de compra, Categorías de producto), Productos (Productos) y Compras (Compras, Kardex).

### 5. Factories (`database/factories/Inventory/`)
- `SupplierFactory`, `PurchaseCategoryFactory`, `ProductCategoryFactory`.

### 6. Controllers (`app/Http/Controllers/Inventory/`)
- `SupplierController`, `PurchaseCategoryController`, `ProductCategoryController` con CRUD completo: index con `withCount` (purchases/products), store/update cacheando `Cache::forget`, y destroy bloqueado si tiene registros relacionados.
- `ProductController`, `PurchaseController`, `KardexController` (stubs, solo `index`).

### 7. Tests Feature (`tests/Feature/Inventory/`)
`SupplierTest`, `PurchaseCategoryTest`, `ProductCategoryTest`: acceso protegido (guest → login), sin permiso → 403, listar, crear, validación de unicidad, actualizar y eliminar. Helpers únicos por archivo (`createSupplierAdmin`, etc.) para evitar colisión global de funciones.

## Assets (Fase 1)
- `npm run build` ejecutado.

## Fase 2 — Productos (`ProductController`)

### Controlador (`app/Http/Controllers/Inventory/ProductController.php`)
CRUD completo: `index` (eager loading de `productCategory`/`purchaseCategory`), `create`, `store`, `edit`, `update`, `destroy`. Para `type = 'dish'` sincroniza los insumos en `product_ingredients` vía `syncIngredients()` (borra y reinserta) dentro de una transacción con la creación/actualización del producto. `destroy` se bloquea si el producto tiene compras o ventas (borra también sus `ingredients`).

### Form Requests (`app/Http/Requests/Inventory/`)
`StoreProductRequest` / `UpdateProductRequest`: `name` requerido, `type` `in:dish,supply,combo`, categorías opcionales (`exists`), precios numéricos ≥ 0, `stock`, `unit_of_measure`, `image_url` url, `is_pos_item`/`status` booleanos, y `ingredients` como arreglo `ingredients.*.ingredient_id` (`exists:products,id`) + `ingredients.*.quantity` (`gt:0`).

### Rutas (`routes/web.php`)
`/productos` → `inventory.products.*`: `index` con `permission:inventario-ver`; `create/store/edit/update/destroy` con `permission:productos-gestionar`.

### Vistas (`resources/views/inventory/products/`)
`index`, `create`, `edit`, `_form`. El `_form` tiene selector de tipo (Plato/Insumo/Combo), categorías, tres precios (venta/costo/POS), stock/unidad, y una sección de ingredientes dinámica (solo para platos) controlada con **JS vanilla** (`data-ingredient-row`, `data-add-ingredient`, `data-remove-ingredient`). No se usó Alpine (no está instalado).

### Factory (`database/factories/Inventory/ProductFactory.php`)
Estados `dish()`, `supply()` (asigna stock), `combo()` y `withCategory()`.

### Tests Feature (`tests/Feature/Inventory/ProductTest.php`)
11 tests: acceso protegido, sin permiso → 403, listar, crear plato/insumo, tipo inválido, actualizar, eliminar sin compras/ventas, y sincronización de ingredientes en store y update.

## Fase 3 — Compras y Kardex

### Controladores (`app/Http/Controllers/Inventory/`)
- `PurchaseController` (CRUD parcial: `index`, `create`, `store`, `show`). En `store`, dentro de una **transacción**: crea la `Purchase` (`status = completed`, `user_id` = usuario autenticado), un `PurchaseDetail` por línea del formulario, **incrementa el `stock`** del producto y genera un `KardexMovement` de tipo `purchase` con `quantity_in`, `balance` = stock tras incrementar, y `related_document` morfado a la compra. Calcula `subtotal`/`total` desde las líneas.
- `KardexController` (solo lectura): `index` con filtro opcional por producto (`?product=`).

### Form Request (`app/Http/Requests/Inventory/StorePurchaseRequest.php`)
Validación de compra: `supplier_id`, `purchase_type` (contado/credito), `series`/`number`, `purchase_date`, y `details[]` (`product_id`, `quantity > 0`, `unit_price >= 0`). `document_type_id` **restringido a `type = 'invoice'`** vía `Rule::exists()->where('type', 'invoice')`.

### Rutas (`routes/web.php`)
- `inventory.purchases.*`: `index`/`show` con `inventario-ver`; `create`/`store` con `compras-gestionar`. `compras/{purchase}` (show) se declara después de `compras/create`.
- `inventory.kardex.index` con `kardex-ver` (reemplaza el stub).

### Vistas (`resources/views/inventory/`)
- `purchases/index.blade.php`: listado con comprobante (serie-número), proveedor, fecha, tipo, total y enlace "Ver".
- `purchases/create.blade.php`: formulario con proveedor, comprobante (tipo invoice), serie/número, fecha y filas de productos dinámicas (JS vanilla `data-line-row`/`data-add-line`/`data-remove-line`).
- `purchases/show.blade.php`: detalle de compra (proveedor, comprobante, líneas, subtotal/total).
- `kardex/index.blade.php`: movimientos con filtro por producto, tipo (compra/venta/ajuste), entradas/salidas/saldo y documento.

### Factories
- `PurchaseFactory`, `PurchaseDetailFactory`, `KardexMovementFactory`.
- Se añadió el estado `invoice()` en `DocumentTypeFactory`.
- `ProductFactory`: se quitó el `afterCreating` que asignaba stock aleatorio en `supply()` para permitir control del stock en tests.

### Tests Feature (`tests/Feature/Inventory/`)
- `PurchaseTest` (8 tests): acceso protegido, sin permiso → 403, listar, registrar compra (verifica subtotal/total, purchase_details, incremento de stock y `KardexMovement` generado), validación de comprobante solo invoice, requerir detalle, ver compra, y acumulación de totales en múltiples líneas.
- `KardexTest` (4 tests): acceso protegido, sin permiso → 403, listar movimientos y filtrar por producto.

## Resultado
- Suite completa: **145 tests, 145 passed** (21 Fase 1 + 11 Fase 2 + 12 Fase 3).
- `vendor/bin/pint` aplicado y limpio.
- 31 rutas registradas bajo `/inventario`.
- **Módulo Inventario completo.**

## Pendiente
- Módulo **POS** (ventas en salón, cobro, preparación en cocina y delivery) — el módulo más grande según el README.