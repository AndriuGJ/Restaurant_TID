# Paso 12 — Módulo POS — Avance 2: Delivery y Venta Rápida

Fecha: 2026-09-02

## Objetivo
Completar el módulo POS con los subflujos **Delivery** (`sale_type = 'delivery'`) y **Venta Rápida** (`sale_type = 'quick_sale'`) que reutilizan la lógica de ventas/cobro del Avance 1, pero **sin mesa** (`sale_type` diferencia los flujos y `is_takeaway` marca el "para llevar").

## Qué se hizo

### 1. Controlador (`app/Http/Controllers/Sales/PosController.php`)
- `newDelivery()` (`pos-delivery`) y `newQuickSale()` (`pos-venta-rapida`): crean una `Sale` **sin `table_id`**, con `sale_type = 'delivery'`/`quick_sale`, `is_takeaway = true`, `subtotal/total = 0`, `status = 'pending'`, y redirigen al flujo estándar `pos.sale`.
- `saveDeliveryInfo(DeliveryInfoRequest, Sale)`: actualiza `delivery_provider_id` y `delivery_person_name` de la venta (PATCH `pos.sale.delivery`).
- `sale()` ahora carga `deliveryProvider` y pasa `$deliveryProviders` activos a la vista.

### 2. Form Request (`app/Http/Requests/Sales/DeliveryInfoRequest.php`)
`delivery_provider_id` (nullable, `exists:delivery_providers`), `delivery_person_name` (nullable, max 150). `authorize()` con `pos-delivery`.

### 3. Rutas (`routes/web.php` — grupo `pos.*`)
- `GET /pos/nueva-venta/delivery` → `pos.new.delivery` (`pos-delivery`).
- `GET /pos/nueva-venta/rapida` → `pos.new.quick-sale` (`pos-venta-rapida`).
- `PATCH /pos/venta/{sale}/delivery` → `pos.sale.delivery` (`pos-delivery`) — se registra **antes** de `venta/{sale}`.

### 4. Vistas (`resources/views/pos/`)
- `hall.blade.php`: botones **Delivery** (teal) y **Venta rápida** (fucsia) arriba, visibles según `pos-delivery` / `pos-venta-rapida`.
- `sale.blade.php`: badge tipo (`DELIVERY` teal, `PARA LLEVAR` fucsia, o "Mesa" para `pos`); para ventas `delivery` muestra un formulario con proveedor (opción "Personal propio") + persona/repartidor.
- `checkout.blade.php`: encabezado muestra `DELIVERY` / `PARA LLEVAR` en vez de mesa.
- `kitchen.blade.php`: badge `DELIVERY` / `PARA LLEVAR` junto al pedido, y para delivery muestra el proveedor/persona en lugar de mesa.

### 5. `KitchenController`
Eager load añadido `sale.deliveryProvider` para poder renderizar el proveedor en la tarjeta de cocina.

### 6. Sidebar (`resources/views/_partials/sidebar.blade.php`)
Grupo **"Nueva venta"** en Punto de Venta con enlaces a Delivery y Venta rápida.

### 7. Tests Feature
- `PosTest` (8 nuevos): guest no puede crear delivery/venta rápida, y usuarios sin `pos-delivery`/`pos-venta-rapida` reciben 403; crear delivery/venta rápida inicia una venta sin mesa con `is_takeaway`; guardar info de delivery actualiza proveedor/persona; y pagar un delivery sin liberar mesa.
- `KitchenTest` (1 nuevo): la cocina muestra la etiqueta `DELIVERY`.

## Resultado
- Suite completa: **172 tests, 172 passed** (164 previos + 8).
- `vendor/bin/pint` aplicado y limpio; `npm run build` OK.
- 3 rutas nuevas bajo `/pos`.
- **Módulo POS completo**: Ventas (salón), Cobro, Preparación (cocina), Delivery y Venta Rápida.

## Pendiente
- Según README sección 7.5 "Cobro": validar `SunatConfig` vigente antes de emitir comprobante (Boleta/Factura), asignar serie-número, e **imprimir comprobante** (PDF con `barryvdh/laravel-dompdf` o WhatsApp) al confirmar el pago.