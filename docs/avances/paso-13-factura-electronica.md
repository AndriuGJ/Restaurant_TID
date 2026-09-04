# Paso 13 — Módulo POS — Factura electrónica (SunatConfig + greenter)

Fecha: 2026-09-02

## Objetivo
Completar el último paso del Cobro del README 7.5: validar el `SunatConfig` vigente **antes** de emitir comprobante de tipo **factura**, asignar **serie-número**, y ofrecer la **impresión/descarga del comprobante** (HTML imprimible + WhatsApp) y el **XML UBL 2.1** generado con **greenter** (no Spatie).

> **Decisión del cliente**: se usa `greenter/greenter` (facturación electrónica SUNAT en Perú) en lugar de `barryvdh/laravel-dompdf`. Spatie se descartó porque **no** publica un paquete de facturación electrónica SUNAT (su `laravel-permission` ya está instalado y no genera facturas).

## Qué se hizo

### 1. Dependencia
`composer require greenter/greenter` (**^4.3**, se instaló v4.3.4). Este paquete produce el XML UBL 2.1 mediante `Greenter\Xml\Builder\InvoiceBuilder`. El wrapper `codersfree/laravel-greenter` se descartó por incompatibilidad con Laravel 13 (acepta `illuminate/console ^12.0` como máximo).

### 2. Validación en `PaySaleRequest` (`after()` → `validateElectronicInvoice`)
Cuando el `document_type` seleccionado es de **tipo `invoice`** (factura), el cobro solo es válido si:
- `clientable_type === 'company'` (empresa con RUC). Si es persona → error `clientable_type`.
- `clientable_id` apunta a un `CompanyClient` existente.
- Existe al menos un `SunatConfig` **vigente**: `status = active`, hoy ∈ `[start_date, end_date]` y `used_receipts < max_receipts`. Si no → error `document_type_id`.

Cualquier error impide el pago (la venta sigue `pending`).

### 3. Asignación de serie/número y conteo en `PosController@pay` (`seriesForSale` / `numberForSale`)
Solo se asigna serie/número cuando el documento es tipo `invoice`:
- **serie** = `DocumentType::nomenclature . '001'` (p.ej. `F` → `F001`).
- **número** = `used_receipts + 1` con 8 dígitos (`str_pad`), e incrementa `used_receipts` del `SunatConfig` activo en la **misma transacción** del cobro.

Después de pagar:
- Si se emitió factura → redirige a `pos.sale.receipt`.
- Si no (boleta/DNI/sin comprobante) → redirige a `pos.hall` (comportamiento previo).

### 4. Comprobante — rutas nuevas
- `GET /pos/venta/{sale}/comprobante` → `pos.sale.receipt` (vista `pos/receipt.blade.php`). Requiere venta `paid` con `series`/`number`.
- `GET /pos/venta/{sale}/comprobante/xml` → `pos.sale.receipt.xml` (descarga XML UBL 2.1 tipo Factura `01`, UBL 2.1, moneda `PEN`, IGV 18%).

### 5. Generación XML (`PosController@buildInvoiceXml`)
Construye el `Greenter\Model\Sale\Invoice` a partir de la `Sale`:
- **Emisor**: `Company` (`emittingCompany()` — la que tiene un `SunatConfig` activo) con RUC, razón social, dirección y datos del `Ubigeo`.
- **Cliente**: `clientable` (CompanyClient: RUC/tipo doc, razón social).
- **Detalles**: uno por `SaleDetail` (unidad `NIU`, cantidad, descripción, precios, `tipAfeIgv = '10'`).
- **Totales**: subtotal gravado, IGV 18%, total, leyenda "SON ... SOLES".
- Via `InvoiceBuilder()->build()` se genera el XML (sin firma digital; la firma/envío a SUNAT queda para producción, pendiente de credenciales SOL + certificado).

### 6. Vista `pos/receipt.blade.php`
Factura imprimible (encabezado emisor + RUC, serie-número, cliente, líneas, subtotal/IGV/total), con botones **Imprimir** (`window.print`, CSS `@media print` solo muestra `#factura`), **Enviar por WhatsApp** (`wa.me/<tel>?text=...` con la ficha) y **Descargar XML**.

### 7. Tests Feature (`PosTest`)
- 1 modificado: `paying a sale registers payments...` ahora usa `identification()` (boleta) porque una factura requiere empresa + SunatConfig.
- 5 nuevos: rechaza factura sin empresa cliente; rechaza factura sin SunatConfig vigente; paga factura asignando serie/número e incrementando `used_receipts` (redirige a `pos.sale.receipt`); el comprobante se renderiza; el XML greenter se descarga.

## Resultado
- Suite completa: **177 tests, 177 passed** (172 previos + 5).
- `vendor/bin/pint` aplicado y limpio.
- 2 rutas nuevas bajo `/pos`.

## Pendiente (producción)
- Elegir/actualizar la **Compañía emisora** correcta y su `ubigeo` para el XML.
- Configurar **firma del XML** (certificado digital `.pfx`/`.pem`), credenciales **SOL** y el **endpoint** de SUNAT (SOAP/API REST) para el envío real y el CDR. Hasta ahora el comprobante y el XML se generan sin enviarse a SUNAT.