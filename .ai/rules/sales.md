---
paths:
  - 'app/Http/Controllers/Sales/**'
  - app/Http/Controllers/Sales/KitchenController.php
  - app/Http/Controllers/Sales/PosController.php
---

# Sales

## POS flow: open-sale lifecycle + stock kardex on pay
POS opens a Sale per table (status pending), reusing the latest open sale; tables become occupied. Adding a SaleDetail from a dish/supply sets kitchen_status=pending and recalculates sale subtotal/total. pay() runs in a transaction: morphs clientable (customer/company), creates SalePayment rows, applyStockKardex decrements stock of type=supply products and creates a KardexMovement movement_type=sale, then releaseTable sets the table back to available. Total must equal the sum of payments (validated in PaySaleRequest::after).

## Kitchen only shows pending SaleDetails, grouped by sale
KitchenController@index filters SaleDetail where kitchen_status=pending, groups by sale_id, and loads sale.table + product. start() sets preparing + prep_started_at; complete() sets completed + prep_completed_at and reverts the sale from preparing back to pending once no pending/preparing details remain. The kitchen view iterates the grouped Collection (each group is a Collection of SaleDetails), not Sales.

## Delivery & quick-sale reuse the tableless sale flow
New delivery and quick-sale orders are created via dedicated GET routes (pos.new.delivery `pos-delivery`, pos.new.quick-sale `pos-venta-rapida`) that start a tableless Sale (table_id null, is_takeaway true) and redirect to the same pos.sale flow. Delivery info (delivery_provider_id, delivery_person_name) is set through the PATCH pos.sale.delivery route using DeliveryInfoRequest. pay()/applyStockKardex()/releaseTable() work identically; releaseTable is a no-op when table_id is null. Kitchen renders DELIVERY/PARA LLEVAR badges from sale.sale_type/is_takeaway.

## Factura/Boleta: SunatConfig por tipo de comprobante (bloques independientes)
Boleta vs Factura se distinguen por `DocumentType->nomenclature` ('B' vs 'F'), NO por `type` (ambos son 'invoice'). Cada SunatConfig se asigna a UN tipo de comprobante vía `document_type_id` (columna nullable, FK a document_types, gateado a type='invoice'); serie = nomenclature+'001' (B001/F001) y correlativo = `used_receipts+1` (8 dígitos), incrementando `used_receipts`. Al cobrar, `PosController@pay`/`numberForSale` y `PaySaleRequest` buscan `activeSunatConfig($document_type_id)` (status=active, hoy en [start_date,end_date], used_receipts<max_receipts). Tanto boleta como factura exigen un bloque vigente con tope; si falta -> error `document_type_id`, no se paga. Solo la factura exige además una empresa (RUC) (`validateFacturaClient`). El XML UBL 2.1 se genera bajo demanda con greenter (InvoiceBuilder) en `pos.sale.receipt.xml`; el comprobante HTML/impresión/WhatsApp está en `pos.sale.receipt`. Spatie NO se usa para facturación; se usa greenter/greenter v4.

## POS requiere caja abierta; ventas se asocian a la sesión activa
POS requiere una caja abierta: el middleware `ensure.pos.session` (alias en bootstrap/app.php) bloquea las rutas de ventas/cobro/delivery/venta rápida si no hay una CashRegisterSession con status=open, redirigiendo a `pos.requires-session`. Se dejan sin bloquear `pos.hall`, `pos.tables.move` y `pos.kitchen.*` (vistas/preparación). Toda Sale creada (open/delivery/quick) se asocia a `cash_register_session_id` de la sesión abierta más reciente (`activeCashRegisterSession()`), de modo que las ventas quedan registradas en la caja hasta su cierre.

## POS: boleta sin RUC, factura requiere empresa, vuelto en sales.change
Boleta vs Factura se distinguen por `documentType->nomenclature` ('B' vs 'F'), NO por `type` (ambos son 'invoice'). La boleta no exige cliente/RUC, pero sí un bloque SUNAT de boletas vigente (ver sección anterior). La factura exige, además del bloque de facturas, una empresa (RUC) ya registrada en Clientes. Los pagos pueden superar el total: el vuelto se calcula como `sum(payments) - total` y se guarda en la columna `sales.change`. El comprobante (receipt) se imprime en ticket de 80 mm y muestra pagos + vuelto.

## Reservas de mesa en POS: lifecycle active→fulfilled/cancelled
Las reservas se crean desde el canvas POS (hall.blade.php): clic izquierdo abre menú contextual (Realizar venta / Reservar mesa / Cancelar reserva), clic derecho abre la venta directo. Rutas POST/DELETE `pos/tables/reserve` y `pos/tables/reservations.cancel` (permiso pos-ventas, SIN ensure.pos.session, igual que tables.move). Al crear reserva la mesa queda status=reserved; al abrir venta en mesa reservada la reserva pasa a fulfilled y mesa occupied; releaseTable vuelve available (o reserved si quedara reserva activa). El modelo Reservation se guarda en tabla reservations (customer_name, people_count, user_id, status enum active/fulfilled/cancelled).
