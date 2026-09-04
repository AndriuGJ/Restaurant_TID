# Paso 5 — Layouts, Autenticación manual y CRUD de Hall (plantilla)

Fecha: 2026-09-02

## Objetivo
Construir el esqueleto funcional del panel: layouts base (app/guest), login manual (sin Breeze), dashboard y el primer CRUD de plantilla (Salones / `Hall`) para repetir el patrón en los demás módulos.

## Qué se hizo

### 1. Partials (`resources/views/_partials/`)
- `header.blade.php`: logo + nombre del usuario y botón de cerrar sesión (form POST a `logout`).
- `sidebar.blade.php`: menú con Dashboard y enlace a Salones y Mesas (visible con `@can('configuracion-ver')`).
- `footer.blade.php`: pie de página con el año y el nombre de la app.

### 2. Autenticación manual (auth con Laravel, sin Breeze)
- `app/Http/Controllers/Auth/LoginController.php`:
  - `create()` → muestra `auth/login`.
  - `store(LoginRequest)` → valida con rate limiting (`RateLimiter`, máx. 5 intentos/ip) y permite iniciar sesión con **email o username**; `Auth::attempt` + `session()->regenerate()`.
  - `destroy()` → logout, invalida sesión y redirige a login.
- `app/Http/Requests/Auth/LoginRequest.php`: validación de `login` y `password` con mensajes en español.
- `resources/views/auth/login.blade.php`: formulario de inicio de sesión (layout `guest`), incluye usuario/correo, contraseña y manejo de errores.

### 3. Dashboard
- `app/Http/Controllers/DashboardController.php` → `view('dashboard.index')`.
- `resources/views/dashboard/index.blade.php`: bienvenida con el nombre del usuario.

### 4. Rutas (`routes/web.php`)
- `/` redirige a `dashboard` si está autenticado, o a `login`.
- `guest`: `GET|POST /login`.
- `auth`: `POST /logout`, `GET /dashboard`, y grupo `restaurante` con las rutas de Hall y permisos finos:
  - `configuracion-ver` → `index`.
  - `configuracion-editar` → `create`, `store`, `edit`, `update`, `destroy`.

### 5. Middleware de Spatie (`bootstrap/app.php`)
- Aliases registrados: `role`, `permission`, `role_or_permission`.

### 6. CRUD de Hall (plantilla de CRUD para el resto de módulos)
- `app/Http/Controllers/Restaurant/HallController.php`: `index` (con `withCount('tables')` + paginación), `create`, `store`, `edit`, `update`, `destroy` (bloqueado si el salón tiene mesas). Limpia cache en escritura.
- `app/Http/Requests/Restaurant/StoreHallRequest.php` y `UpdateHallRequest.php`: reglas `name` (requerido, máx. 100, único) y `status` (booleano), mensajes en español.
- Vistas `resources/views/restaurant/halls/`: `index`, `create`, `edit` y partial `_form` reutilizable.
- `app/Http/Requests` y controllers dentro de subcarpetas por módulo (convención del proyecto).

### 7. Paginación Tailwind
- Publicado el tag `laravel-pagination` y `Paginator::useTailwind()` en `AppServiceProvider`.

### 8. Factories y tests
- `database/factories/Restaurant/HallFactory.php` y `TableFactory.php`.
- `tests/Feature/Auth/LoginTest.php`: login por email y username, login inválido, logout, protección de rutas.
- `tests/Feature/Restaurant/HallTest.php`: permisos (`configuracion-ver`/`editar`), listado, crear (y validación única), editar, actualizar, eliminar y la regla de no eliminar salones con mesas.
- Nota: en `tests/Pest.php` se descomentó `->use(RefreshDatabase::class)` para que las migraciones corran en los tests de Feature (antes estaba desactivado, causaba "no such table").

## Assets
- `npm run build` ejecutado; manifest de Vite generado y CSS/JS compilados.

## Resultado
- Suite completa: **19 tests, 19 passed**.
- `vendor/bin/pint` sin cambios pendientes.

## Pendiente
- `welcome.blade.php` quedó huérfana (ya no la usa la ruta `/`).
- CRUD de Table, Shift, CashRegister y DeliveryProvider (mismo patrón que Hall).
