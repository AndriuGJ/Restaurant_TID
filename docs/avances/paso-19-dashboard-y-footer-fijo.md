# Paso 19 — Dashboard con métricas, accesos rápidos y footer fijo al fondo

Fecha: 2026-09-03

## Objetivo
- Reemplazar el dashboard placeholder por un **panel real**: métricas del día, accesos directos y alertas de stock.
- **Arreglar el footer**: que siempre quede al fondo de la ventana aunque el contenido sea corto.

## Qué se hizo

### 1. Footer siempre abajo (layout `app`)
Antes el layout usaba `<div class="min-h-full">` con el contenido en flujo: con poco contenido el footer quedaba pegado al contenido y no al fondo.
Ahora la estructura es una **columna flex de altura completa**:
- `body.h-full`
- wrapper `div.flex.min-h-full.flex-col`
- `header` → `sticky top-0`
- fila `div.flex.flex-1` (sidebar + main)
- `footer` al final (`shrink-0`)

El sidebar de escritorio pasó a `sticky top-16 h-[calc(100vh-4rem)] overflow-y-auto` (scroll interno propio, queda fijo al scrollear).

### 2. Dashboard con métricas (`DashboardController` + `dashboard/index`)
El controlador consulta (solo lectura) y pasa dos variables:
- **`$stats`**:
  - `salesToday` / `salesTodayTotal` → ventas pagadas de hoy + monto.
  - `kitchenPending` → órdenes en cocina (pending/preparing).
  - `lowStockCount` / `lowStockProducts` → productos con `stock <= 10`.
  - `openCashSession` → sesión de caja abierta (si existe) con su `opening_amount`.
  - `usersCount`.
- **`$shortcuts`**: accesos rápidos (Salón, Venta rápida, Delivery, Cocina, Reporte de ventas, Caja, Productos, Clientes), **filtrados por permiso** del usuario (`can`).

La vista muestra:
- Tarjetas de estadísticas (4 columnas en escritorio): Ventas hoy, En cocina, Stock bajo, Estado de caja.
- Grilla de **accesos rápidos** con iconos y hover.
- Lista de **productos por reponer** (alertas de stock).

### 3. Componente de iconos
Nuevo componente lámina **`resources/views/components/dashboard-icon.blade.php`**: `<x-dashboard-icon icon="hall" class="...">` mapea una clave a un SVG Heroicons (outline) inline, evitando repetir paths en la vista.

## Test agregado
`tests/Feature/DashboardTest.php` (5 tests): acceso solo autenticado, total de ventas de hoy, alerta de stock bajo, y estado de caja abierta.

## Verificación
- Suite completa: **219/219** tests pasando (214 previos + 5 nuevos).
- `vendor/bin/pint` limpio; `npm run build` OK.

## Próximo
Aplicar la misma identidad a las **tarjetas/tablas** de los CRUDs y pantallas del sistema para uniformar todo el panel.