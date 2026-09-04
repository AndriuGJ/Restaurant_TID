# Paso 14 — Reportes (Ventas y Cajas) + POS requiere caja abierta

Fecha: 2026-09-02

## Objetivo
1. **Módulo de Reportes**:
   - **Reporte de Ventas**: listado/consolidado de ventas **pagadas** (salón `pos`, `delivery` y `venta rápida`) con resumen de totales, similar al Kardex pero especializado en ventas.
   - **Reporte de Cajas**: historial de aperturas y cierres de caja — montos de apertura/cierre, dinero recaudado, responsables y fechas — para tener trazabilidad completa.
2. **Cambio en POS**: el punto de venta **requiere una caja abierta** para funcionar. Todas las ventas se registran en la sesión de caja activa (`cash_register_session_id`) hasta que la caja se cierra, y al abrir de nuevo la caja se **arranca con el monto de cierre anterior** (carry-forward, editable).

## Qué se hizo

### 1. Permisos nuevos (`RolePermissionSeeder`)
- `reportes-ver` (reporte de ventas) y `reportes-cajas-ver` (reporte de cajas). Se asignan a `administrador` y `contabilidad`. El POS ya existía; el requisito de caja no añade permisos nuevos (usa `cajas-abrir`/`cajas-cerrar` existentes).

### 2. POS requiere caja abierta
- Nuevo middleware `app/Http/Middleware/EnsurePosSessionOpen.php`, registrado como alias `ensure.pos.session` en `bootstrap/app.php`.
- Si **no** existe una `CashRegisterSession` con `status = 'open'`, las rutas de ventas/cobro redirigen a `pos.requires-session` (nueva vista `pos/requires-session.blade.php` que enlaza a "Abrir caja" si hay permiso).
- Se bloquean las rutas de operación de venta (`pos.open`, `pos.new.delivery`, `pos.new.quick-sale`, `pos.sale`, `pos.sale.add-product`, `pos.sale.detail.update/remove`, `pos.sale.kitchen`, `pos.checkout`, `pos.pay`, `pos.sale.receipt`, `pos.sale.receipt.xml`, `pos.sale.cancel`). Se **dejan abiertas** `pos.hall`, `pos.tables.move` y `pos.kitchen.*` (ver salón/preparación no necesita caja).

### 3. Registro de ventas en la caja activa
`PosController::activeCashRegisterSession()` devuelve la sesión abierta más reciente. Toda `Sale::create()` (apertura de mesa, `newDelivery`, `newQuickSale`) ahora guarda `cash_register_session_id`. Así **las ventas de salón, delivery y venta rápida quedan en la caja abierta** hasta su cierre.

### 4. Carry-forward del monto de cierre (`CashRegisterSessionController::create`)
La vista `cash-registers/create.blade.php` ahora **pre-llena** el campo `opening_amount` con el `closing_amount` de la última sesión **cerrada** de la caja; es editable y un script JS actualiza el monto al cambiar de caja. Ya no es solo una sugerencia visual.

### 5. Módulo de Reportes
- Controladores: `app/Http/Controllers/Reports/VentasReportController.php` y `CajasReportController.php`.
- Rutas bajo `/reportes` (`reportes.ventas` con `reportes-ver`, `reportes.cajas` con `reportes-cajas-ver`).
- Vistas: `resources/views/reports/ventas.blade.php` y `reports/cajas.blade.php` (filtros + resumen + tabla + `window.print`).
- Sidebar: menú **Reportes** (Ventas, Cajas); cada item respeta su permiso individual.

## Resultado
- Suite completa: **191 tests, 191 passed** (177 previos + 14).
- `vendor/bin/pint` aplicado; `npm run build` OK.
- 3 rutas nuevas: `pos.requires-session`, `reportes.ventas`, `reportes.cajas`.

## Notas / pendiente
- El requisito de caja bloquea solo la operación de venta, no la cocina ni el salón (el rol `chef` sigue viendo su cocina sin caja).
- El monto de cierre se captura manualmente en el cierre (puede compararse contra el `sales_sum_total` del reporte de cajas).