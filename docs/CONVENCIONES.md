# Convenciones de Estructura del Proyecto

Este documento fija las convenciones de arquitectura y código que deben seguirse en todo el proyecto.

## Controllers por módulo
Los controllers se organizan en subcarpetas por módulo, igual que los modelos.
- Estructura: `app/Http/Controllers/<Módulo>/<Entidad>Controller.php`
- Ejemplo: `app/Http/Controllers/Restaurant/HallController.php`, `app/Http/Controllers/Restaurant/TableController.php`.
- Namespace: `App\Http\Controllers\<Módulo>\<Entidad>Controller`.

## Form Requests (validaciones aparte)
Toda la validación se hace en **Form Requests** dedicados, no en los controllers. Así las validaciones quedan separadas de la lógica de cada entidad.
- Estructura: `app/Http/Requests/<Módulo>/Store<Entidad>Request.php` y `app/Http/Requests/<Módulo>/Update<Entidad>Request.php`.
- El controller recibe el Request tipado en el método (inyección de dependencias) y Laravel valida automáticamente.
- Ejemplo: `HallController@store(StoreHallRequest $request)`.

## Frontend
- **Blade** como motor principal de vistas.
- **Livewire** solo en pantallas donde se necesite no recargar la página (componentes interactivos).
- Tailwind CSS para los estilos (ya configurado via Vite).

## Layouts Blade
- `resources/views/layouts/app.blade.php` → layout autenticado/panel (sidebar, header, footer).
- `resources/views/layouts/guest.blade.php` → layout público (login/registro).
- Partials reutilizables en `resources/views/_partials/`:
  - `_partials/header.blade.php`
  - `_partials/sidebar.blade.php`
  - `_partials/footer.blade.php`
- El layout `app` incluye sus partials; las vistas hijas (CRUDs, etc.) se renderizan dentro de `app`.

## Rutas
- Rutas protegidas por middleware `auth` + `role:`/`can:` según cada módulo.
- Preferir rutas con nombre y `route()` en las vistas.
