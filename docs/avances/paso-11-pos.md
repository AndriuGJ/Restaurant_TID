# Paso 11 — Módulo POS (Ventas en Salón, Cobro y Cocina)

Fecha: 2026-09-02

## Objetivo
Implementar el módulo **Punto de Venta (POS)** según la sección 7.5 del README: salón interactivo con un canvas de mesas movibles por arrastre, apertura de ventas por mesa, agregado de productos al pedido, envío a cocina, cobro con pagos divididos y pantalla de preparación en cocina. Al pagar, el stock de insumos se decrementa y se genera el movimiento de kardex de salida.

## Qué se hizo

### 1. Rutas (`routes/web.php`) — grupo `pos.*`
Dentro del grupo `auth`, con `prefix('pos')->name('pos.')`:
- `GET /pos` → `pos.hall` (`pos-ver`) — canvas del salón.
- `PUT /pos/mesas/{table}/mover` → `pos.tables.move` (`pos-ver`) — JSON, guarda `pos_x`/`pos_y`.
- `GET /pos/mesas/{table}/abrir` → `pos.open` (`pos-ventas`) — abre o reutiliza una venta de la mesa.
- `GET /pos/venta/{sale}` → `pos.sale` (`pos-ventas`).
- `POST /pos/venta/{sale}/producto` → `pos.sale.add-product` (`pos-ventas`).
- `PATCH /pos/detalle/{detail}` → `pos.sale.detail.update`; `DELETE /pos/detalle/{detail}` → `pos.sale.detail.remove`.
- `POST /pos/venta/{sale}/cocina` → `pos.sale.kitchen` (`pos-ventas`) — envía a cocina.
- `GET /pos/venta/{sale}/cobro` → `pos.checkout` (`pos-cobro`).
- `POST /pos/venta/{sale}/pagar` → `pos.pay` (`pos-cobro`).
- `DELETE /pos/venta/{sale}` → `pos.sale.cancel` (`pos-ventas`).
- `GET /pos/cocina` → `pos.kitchen.index`; `POST /pos/cocina/detalle/{detail}/iniciar` → `pos.kitchen.start`; `POST /pos/cocina/detalle/{detail}/completar` → `pos.kitchen.complete` (todas `pos-preparacion`).

`pos` exacto se registra antes que `pos/venta/{sale}` y `pos/cocina` para evitar conflictos de ruta.

### 2. Migración: posiciones de mesa
`2026_09_02_175734_add_positions_to_tables_table.php`: añade `pos_x`, `pos_y` (unsignedInteger, nullable) a `tables`. `App\Models\Restaurant\Table` incluye ambas en `$fillable`.

### 3. Controlladores (`app/Http/Controllers/Sales/`)
- `PosController`:
  - `hall()`: lista salones activos con sus mesas, `arrangeAutoTables()` asigna posición automática a mesas sin `pos_x`.
  - `moveTable()`: JSON que guarda la posición.
  - `openSale()` / `openSaleForTable()`: reutiliza la venta `pending`/`preparing` más reciente de la mesa, o crea una nueva (`sale_type=pos`) y marca la mesa `occupied`.
  - `addProduct()`: crea `SaleDetail` (`kitchen_status=pending`) y recalcula totales.
  - `updateDetailQuantity()` / `removeDetail()`: ajusta cantidades/totales.
  - `sendToKitchen()`: marca la venta `preparing` si hay líneas pendientes.
  - `checkout()`: pantalla de cobro (cliente, documento invoice, medios de pago).
  - `pay()`: **transacción** — asigna cliente polimórfico (`customer`/`company`), `document_type_id`, crea `SalePayment` por línea, `applyStockKardex()` (decrementa stock de `type=supply` y genera `KardexMovement` `movement_type=sale`, `related_document` → Sale) y `releaseTable()` (mesa `available`).
  - `cancel()`: anula la venta y libera la mesa.
- `KitchenController`: `index()` devuelve los `SaleDetail` con `kitchen_status=pending` **agrupados por sale** (con `sale.table` y `product`); `start()` pasa a `preparing` + `prep_started_at`; `complete()` pasa a `completed` + `prep_completed_at` y revierte la venta `preparing` → `pending` cuando no quedan líneas pendientes/en preparación.

### 4. Form Requests (`app/Http/Requests/Sales/`)
- `MoveTableRequest`: `pos_x`/`pos_y` enteros ≥ 0 (`pos-ver`).
- `AddProductRequest`: `product_id` (exists), `quantity > 0`, `notes` opcional (`pos-ventas`).
- `PaySaleRequest`: `clientable_type` `in:customer,company` + `clientable_id` requerido con el tipo, `document_type_id`, `guests`, `notes`, `payments[]` (`payment_method_id` exists, `amount > 0`); `after()` valida que la suma de pagos coincida con el total de la venta (`pos-cobro`).

### 5. Vistas (`resources/views/pos/`)
- `hall.blade.php` + `_table-node.blade.php`: **canvas interactivo** con **JS vanilla** — mesas absolutas arrastrables por Pointer Events; al soltarlas se guarda la posición vía un `PUT` con `fetch()`. Estados de mesa coloreados (occupied ámbar, reserved azul, available verde), selector de salón (`?hall=`), enlace a cocina (solo `pos-preparacion`) y panel de estado.
- `sale.blade.php`: selector de productos por categoría + buscador (JS vanilla `pos-search`/`pos-categories`), cantidad/nota, detalle del pedido con edición de cantidad y quitar línea, botones **Enviar a cocina** y **Cobrar**.
- `checkout.blade.php`: resumen, cliente (persona/empresa) y documento invoice, comensales, **pagos divididos** dinámicos (`payments[][payment_method_id|amount]`) con suma y validación en vivo.
- `kitchen.blade.php`: tarjetas de pedidos pendientes agrupadas por venta, con detalle y botones **Iniciar**/**Completar** por línea.

### 6. Factories (`database/factories/Sales/`)
`SaleFactory` (estados `atTable`, `paid`), `SaleDetailFactory` (estados `dish`, `preparing`, `completed`), `SalePaymentFactory`, `SaleTableFactory`.

### 7. Sidebar (`resources/views/_partials/sidebar.blade.php`)
Nueva sección **"Punto de Venta"** (permiso `pos-ver`) con grupos Salón (Salón) y Cocina (Preparación).

### 8. Tests Feature (`tests/Feature/Sales/`)
- `PosTest` (12 tests): guest → login, sin permiso → 403, listar salón, abrir mesa (crea venta + mesa ocupada), reutilizar venta abierta, agregar producto (actualiza totales), validar producto/cantidad, enviar a cocina (preparing), pagar (pagos, cliente, stock/kardex, libera mesa), rechazar total desajustado, cancelar (libera mesa), mover mesa, y render de páginas de venta y cobro.
- `KitchenTest` (7 tests): guest → login, sin permiso → 403, mostrar pedidos pendientes agrupados, ocultar completados, iniciar (preparing), completar (completed + venta reverte a pending), render.

Helpers únicos por archivo (`createPosAdmin`, `createKitchenAdmin`) para evitar colisión global de funciones entre archivos.

## Resultado
- Suite completa: **164 tests, 164 passed** (146 previos + 19 de Sales).
- `vendor/bin/pint` aplicado y limpio.
- `npm run build` OK (Tailwind v4 + Vite).
- 15 rutas nuevas bajo `/pos` (12 POS + 3 cocina).
- **Módulo POS (avance 1): ventas en salón, cobro y preparación en cocina funcionales.**

## Pendiente
- Facturación/boleta electrónica (serie-número/CAF), módulo **Delivery** y **Venta rápida** (permisos `pos-delivery`, `pos-venta-rapida`) según README sección 7.5.