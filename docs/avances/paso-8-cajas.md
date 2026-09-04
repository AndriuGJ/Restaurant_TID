# Paso 8 — Módulo Cajas (apertura/cierre de sesiones de caja)

Fecha: 2026-09-02

## Objetivo
Implementar el módulo **Cajas** según la sección 7.3 del README: abrir y cerrar una caja ya existente (creada en Configuración → Restaurante) mediante sesiones (`CashRegisterSession`). No es un CRUD de la entidad `CashRegister` (eso ya vive en Configuración), sino la operación de apertura/cierre con estado `open`/`closed`.

## Qué se hizo

### 1. Controlador `CashRegisters\CashRegisterSessionController`
Métodos:
- `index`: lista paginada de sesiones con caja, turno, usuario de apertura (y cierre), montos, estado y fechas (eager loading de `cashRegister`, `shift`, `userOpening`, `userClosing`).
- `create`: formulario de apertura. Pasa cajas (con `withCount` de sesiones), turnos y la sugerencia de monto.
- `store`: crea la sesión `open`, registrando `user_opening_id` (usuario logueado) y `opened_at` (ahora).
- `edit`: vista de cierre de una sesión abierta.
- `update`: cierra la sesión guardando `user_closing_id`, `closing_amount`, `status = closed` y `closed_at`.
- `suggestedOpeningAmount()` (estático/helper): **regla de negocio del documento** — devuelve el `closing_amount` de la última sesión cerrada de una caja para sugerirlo como monto de apertura.

### 2. Form Requests (`app/Http/Requests/CashRegisters/`)
- `OpenCashRegisterSessionRequest`: valida `cash_register_id` (existe), `shift_id` (existe), `opening_amount` (>= 0). Además, en `withValidator` **rechaza abrir una caja que ya tenga una sesión `open`**.
- `CloseCashRegisterSessionRequest`: valida `closing_amount` (>= 0).

### 3. Rutas (`routes/web.php`)
Subgrupo `Route::prefix('cajas')->name('cash-registers.sessions.')`:
- `GET cajas` → `index` (permiso `cajas-ver`)
- `GET cajas/abrir` → `create` (`cajas-abrir`)
- `POST cajas` → `store` (`cajas-abrir`)
- `GET cajas/{session}/cerrar` → `edit` (`cajas-cerrar`)
- `PUT cajas/{session}` → `update` (`cajas-cerrar`)

Usa los permisos ya definidos en `RolePermissionSeeder`: `cajas-ver`, `cajas-abrir`, `cajas-cerrar`.

### 4. Vistas (`resources/views/cash-registers/`)
- `index`: tabla de sesiones con badge Abierta/Cerrada y botón "Cerrar" (solo si está abierta y el usuario tiene `cajas-cerrar`); botón "Abrir caja" (con `cajas-abrir`).
- `create` (abrir): selects de caja y turno, campo monto de apertura con **sugerencia del cierre anterior por caja** mostrada como ayuda.
- `edit` (cerrar): resumen de la sesión y campo de monto de cierre (precargado con el monto de apertura).

### 5. Sidebar (`resources/views/_partials/sidebar.blade.php`)
Nueva sección independiente **"Cajas"** (permiso `cajas-ver`) con el grupo "Sesiones" e ítem "Apertura / Cierre".

### 6. Factory (`database/factories/Restaurant/CashRegisterSessionFactory.php`)
Registro `open` por defecto (crea su `CashRegister`, `Shift` y `User` de apertura) y estado `closed()` con usuario/monto/fecha de cierre.

### 7. Tests Feature (`tests/Feature/CashRegisters/CashRegisterSessionTest.php`)
Cubren: acceso protegido (guest → login), sin permiso → 403, listar, abrir, **evitar doble apertura de la misma caja**, monto de apertura requerido, cerrar, monto de cierre requerido y acceso a la vista de cierre.

## Assets
- `npm run build` ejecutado (clases Tailwind nuevas de las vistas).

## Resultado
- Suite completa: **86 tests, 86 passed** (9 nuevos del módulo Cajas).
- `vendor/bin/pint` aplicado y limpio en archivos nuevos.
- 5 rutas registradas bajo `/cajas`.

## Pendiente
- Módulo de **Clientes** (`Customer`, `CompanyClient`) — siguiente paso del README.