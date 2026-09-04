# Paso 6 — Sidebar escalable + CRUDs de Configuración → Restaurante (Tables, Shifts, Cash Registers, Delivery Providers)

Fecha: 2026-09-02

## Objetivo
Completar el CRUD del módulo **Configuración → Restaurante** (Hall ya se hizo en el paso 5 como plantilla) y rediseñar el sidebar para que maneje submodulos de forma escalable y colapsable.

## Qué se hizo

### 1. Sidebar escalable (`resources/views/_partials/sidebar.blade.php`)
- Rediseñado como **menú data-driven**: se define un array `$menu` con secciones → grupos → ítems (cada ítem con `label` + `route`).
- Cada sección se muestra u oculta según permiso (`@can`).
- Los grupos se colapsan con `<details>/<summary>` nativos de HTML (sin JS); flecha giratoria con clases de Tailwind (`group-open:rotate-180`).
- Escalable: para añadir un módulo solo se agrega una sección/grupo al array.
- El ítem activo se resalta si `request()->routeIs($item['route'])`.

### 2. Partials reutilizables (`resources/views/_partials/`)
- `status-badge.blade.php`: badge Activo/Inactivo reutilizable en las tablas index.
- `row-actions.blade.php`: botones Editar/Eliminar (con confirm) reutilizables.

### 3. CRUD de Table (Mesas) — `Restaurant\TableController`
- Campos: `hall_id` (FK), `name`, `shape` (square/round/rectangular), `status` (available/occupied/reserved).
- Validación única del nombre **por salón** (`where hall_id`).
- No se elimina una mesa que tenga ventas asociadas (`sales`/`saleTables`).
- Formulario con selects de salón, forma y estado; vistas en `restaurant/tables/`.

### 4. CRUD de Shift (Turnos) — `Restaurant\ShiftController`
- Campos: `name`, `status`. No se elimina si tiene sesiones de caja.

### 5. CRUD de CashRegister (Cajas) — `Restaurant\CashRegisterController`
- Campos: `name`, `status`. No se elimina si tiene sesiones registradas.

### 6. CRUD de DeliveryProvider (Delivery) — `Restaurant\DeliveryProviderController`
- Campos: `name`, `phone`, `contact_person`, `status`. No se elimina si tiene ventas.

### 7. Form Requests por entidad (`app/Http/Requests/Restaurant/`)
- `Store/Update` para Shift, CashRegister, DeliveryProvider y Table (reglas + mensajes en español).

### 8. Rutas (`routes/web.php`)
- Agregadas las rutas explícitas de `tables`, `shifts`, `cash-registers` y `delivery-providers` bajo el prefijo `restaurante`, aplicando permisos finos: `configuracion-ver` en index y `configuracion-editar` en create/store/edit/update/destroy.

### 9. Factories y tests
- Factories: `ShiftFactory`, `CashRegisterFactory`, `DeliveryProviderFactory` (TableFactory ya existía).
- Tests Feature por entidad (`ShiftTest`, `CashRegisterTest`, `DeliveryProviderTest`, `TableTest`) que cubren: acceso protegido, permiso, listar, crear (+ validaciones), editar, actualizar y eliminar.
- **Trampa resuelta**: en Pest las funciones globales colisionan entre archivos ("Cannot redeclare adminUser"). Se renombraron a nombres únicos por archivo (`createShiftAdmin`, `createTableAdmin`, etc.).

### 10. Se eliminó la carpeta `resources/views/vendor/pagination`
- Se borró (el usuario pidió no necesitarla). La paginación sigue funcionando con la vista Tailwind interna de Laravel (`Paginator::useTailwind()`).

## Assets
- `npm run build` ejecutado con las nuevas clases Tailwind del sidebar y vistas.

## Resultado
- Suite completa: **48 tests, 48 passed**.
- `vendor/bin/pint` sin cambios pendientes.
- Verificación end-to-end: login admin + acceso 200 a halls, tables, shifts, cash-registers y delivery-providers.

## Pendiente
- CRUD de Configuración → Sistema: `DocumentType`, `PaymentMethod`, `Company`, `SunatConfig` (paso siguiente del README).
