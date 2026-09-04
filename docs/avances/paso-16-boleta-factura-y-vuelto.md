# Paso 16 — Boleta y Factura diferenciadas + cálculo de vuelto en POS

Fecha: 2026-09-02

## Objetivo
1. **Diferenciar Boleta y Factura** en el cobro del POS:
   - **Boleta**: se emite **siempre** sin exigir RUC (compra sin identificar). Ticket de 80 mm.
   - **Factura**: requiere una **empresa (RUC) ya registrada en Clientes**, que se selecciona al pagar. Misma estructura de ticket, pero con datos del cliente.
2. **Pago con vuelto**: permitir que el cliente pague "de más" (ej. S/20, S/50, S/100) cuando paga en efectivo, calcular el **vuelto** y mostrar tanto en pantalla como en el comprobante.

## Qué se hizo

### 1. Base de datos
- Nueva migración `add_change_to_sales_table`: columna `sales.change` (decimal 10,2, default 0.00).
- `Sale` model: `change` en `fillable` y en `casts` (`decimal:2`).

### 2. Validación del pago (`PaySaleRequest`)
- Antes: la suma de pagos debía **coincidir exacto** con el total (no dejaba pagar de más).
- Ahora: la suma de pagos debe ser **>= total**. Si es mayor, hay vuelto.
- Antes: seleccionar Boleta o Factura exigía empresa/RUC porque ambas eran `type='invoice'`.
- Ahora: solo la **Factura** (`nomenclature === 'F'`) exige empresa (RUC) registrada y bloque SUNAT vigente. La **Boleta** (`nomenclature === 'B'`) se emite sin cliente.

### 3. Controlador (`PosController::pay`)
- Calcula `$change = max(suma_pagos - total, 0)` y lo guarda en `sales.change`.
- Redirige al comprobante tanto para boleta como para factura (ambas emiten).
- `buildInvoiceXml()` ahora genera **boleta (tipo 03)** o **factura (tipo 01)** según `nomenclature`, con cliente por defecto "PÚBLICO GENERAL" para boletas sin cliente.

### 4. Vista de cobro (`pos/checkout.blade.php`)
- El selector de comprobante muestra una pista contextual: boleta no necesita RUC; factura requiere empresa.
- Al seleccionar factura, la UI recuerda seleccionar "Empresa".
- La suma de pagos indica en vivo si "falta" o el **vuelto** (en verde) cuando se paga de más.

### 5. Comprobante (`pos/receipt.blade.php`)
- Rediseñado como **ticket térmico de 80 mm** (impresión `width: 80mm`), para boleta y factura.
- Título dinámico: **BOLETA ELECTRÓNICA** / **FACTURA ELECTRÓNICA**.
- Factura muestra Cliente + RUC; boleta no.
- Detalle de ítems, subtotal, IGV (18%), total.
- **Pagos por medio**, total pagado, y **VUELTO** (cuando aplica).

## Resultado
- Suite completa: **212 tests, 212 passed** (210 previos + 2 nuevos: boleta sin RUC y vuelto).
- `vendor/bin/pint` limpio; `npm run build` OK; migración aplicada a la BD local.
- Nueva columna `sales.change` y factory state `boleta()` en `DocumentTypeFactory`.

## Notas
- El vuelto se calcula como la diferencia entre lo cobrado y el total; no se valida que el medio sea efectivo, para no bloquear pagos mixtos.
- El envío/recepción real a SUNAT (firma y CDR) sigue pendiente en producción.