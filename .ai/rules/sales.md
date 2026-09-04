---
paths:
  - 'app/Http/Controllers/Sales/**'
  - app/Http/Controllers/Sales/KitchenController.php
---

# Sales

## POS flow: open-sale lifecycle + stock kardex on pay
POS opens a Sale per table (status pending), reusing the latest open sale; tables become occupied. Adding a SaleDetail from a dish/supply sets kitchen_status=pending and recalculates sale subtotal/total. pay() runs in a transaction: morphs clientable (customer/company), creates SalePayment rows, applyStockKardex decrements stock of type=supply products and creates a KardexMovement movement_type=sale, then releaseTable sets the table back to available. Total must equal the sum of payments (validated in PaySaleRequest::after).

## Kitchen only shows pending SaleDetails, grouped by sale
KitchenController@index filters SaleDetail where kitchen_status=pending, groups by sale_id, and loads sale.table + product. start() sets preparing + prep_started_at; complete() sets completed + prep_completed_at and reverts the sale from preparing back to pending once no pending/preparing details remain. The kitchen view iterates the grouped Collection (each group is a Collection of SaleDetails), not Sales.

## Delivery & quick-sale reuse the tableless sale flow
New delivery and quick-sale orders are created via dedicated GET routes (pos.new.delivery `pos-delivery`, pos.new.quick-sale `pos-venta-rapida`) that start a tableless Sale (table_id null, is_takeaway true) and redirect to the same pos.sale flow. Delivery info (delivery_provider_id, delivery_person_name) is set through the PATCH pos.sale.delivery route using DeliveryInfoRequest. pay()/applyStockKardex()/releaseTable() work identically; releaseTable is a no-op when table_id is null. Kitchen renders DELIVERY/PARA LLEVAR badges from sale.sale_type/is_takeaway.

## Factura electrónica: SunatConfig vigente + serie/número + XML greenter
Al cobrar (`PosController@pay`) un comprobante tipo `invoice`, la validación en PaySaleRequest exige cliente empresa (CompanyClient) y un SunatConfig activo (status=active, hoy en [start_date,end_date] y used_receipts < max_receipts). Sin config vigente o sin empresa -> error de validación, no se paga. La serie se deriva de DocumentType::nomenclature+'001' (ej 'F001') y el correlativo de used_receipts+1 con 8 dígitos, incrementando used_receipts en la misma transacción. El XML UBL 2.1 se genera bajo demanda con greenter (InvoiceBuilder) en `pos.sale.receipt.xml`; el comprobante HTML/impresión/WhatsApp está en `pos.sale.receipt`. Spatie NO se usa para facturación (no existe paquete SUNAT); se usa greenter/greenter v4.

## POS requiere caja abierta; ventas se asocian a la sesión activa
POS requiere una caja abierta: el middleware `ensure.pos.session` (alias en bootstrap/app.php) bloquea las rutas de ventas/cobro/delivery/venta rápida si no hay una CashRegisterSession con status=open, redirigiendo a `pos.requires-session`. Se dejan sin bloquear `pos.hall`, `pos.tables.move` y `pos.kitchen.*` (vistas/preparación). Toda Sale creada (open/delivery/quick) se asocia a `cash_register_session_id` de la sesión abierta más reciente (`activeCashRegisterSession()`), de modo que las ventas quedan registradas en la caja hasta su cierre.

## POS: boleta sin RUC, factura requiere empresa, vuelto en sales.change
Boleta vs Factura se distinguen por `documentType->nomenclature` ('B' vs 'F'), NO por `type` (ambos son 'invoice'). La boleta se emite siempre, sin exigir cliente/RUC. La factura exige una empresa (RUC) ya registrada en Clientes y un bloque SUNAT vigente. Los pagos pueden superar el total: el vuelto se calcula como `sum(payments) - total` y se guarda en la columna `sales.change` (nuevo). El comprobante (receipt) se imprime en ticket de 80 mm y muestra pagos + vuelto.
