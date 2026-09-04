# Paso 15 — Módulo de Usuarios y Roles, y fotos de platos en POS

Fecha: 2026-09-02

## Objetivo
1. **Módulo de Usuarios**: poder crear usuarios para que ingresen al sistema (login por email o username).
2. **Roles y permisos desde Usuarios**: asignar roles a cada usuario y **gestionar roles/permisos** (crear, editar, eliminar roles y marcar sus permisos).
3. **Fotos en POS**: mostrar los platos con imagen en el catálogo del punto de venta (no solo el nombre).

## Qué se hizo

### 1. Permisos nuevos (`RolePermissionSeeder`)
- `usuarios-ver` — ver la lista de usuarios.
- `usuarios-gestionar` — crear/editar/eliminar usuarios y asignarles roles.
- `roles-gestionar` — gestionar roles y sus permisos.
- El rol `administrador` recibe todos (por ser `$permissions` completo).

### 2. Módulo de Usuarios
- `App\Http\Controllers\Users\UserController` (CRUD completo).
- Form Requests: `StoreUserRequest` / `UpdateUserRequest`.
- Vistas: `resources/views/users/{index,create,edit,_form}.blade.php`.
- Rutas bajo `/usuarios` con `users.` prefix y middleware `usuarios-*`.
- Campos: DNI (8 dígitos), nombre, apellido, email (único), username (único), contraseña (+ confirmación), cargo, estado activo.
- **Asignación de roles** por checkboxes; todo usuario requiere **al menos un rol**.
- Al editar, la contraseña solo se actualiza si se llena (en blanco = se mantiene la actual).
- No se permite eliminar el propio usuario.

### 3. Roles y permisos
- `App\Http\Controllers\Users\RoleController` (CRUD + `syncPermissions`).
- Form Requests: `StoreRoleRequest` / `UpdateRoleRequest`.
- Vistas: `resources/views/users/roles/{index,create,edit,_form}.blade.php`.
- El nombre del rol se guarda como **slug** (ej. "Cajero" → `cajero`).
- Los permisos se presentan **agrupados por módulo** (Configuración, Punto de venta, Clientes, Cajas, Inventario, Reportes, Usuarios, General) con checkboxes.
- El rol `administrador` no puede eliminarse.

### 4. Sidebar
- Nuevo menú **Usuarios** (Usuarios + Roles y permisos), con permiso por ítem.

### 5. Fotos de platos en POS
- `PosController::sale()` ahora incluye `image_url` en la consulta de productos.
- `resources/views/pos/sale.blade.php`: cada botón de producto muestra la **imagen** (`object-cover`) si existe; si no, muestra un **placeholder** (icono de plato) — el nombre y precio se mantienen debajo.

## Resultado
- Suite completa: **210 tests, 210 passed** (191 previos + 19 nuevos).
- `vendor/bin/pint` limpio; `npm run build` OK; permisos nuevos registrados en DB y rol `administrador` actualizado.
- 12 rutas nuevas de `users.*`/`users.roles.*`.

## Notas
- El login funciona por email o username y acepta a cualquier usuario creado (la contraseña se guarda hasheada vía cast).
- La imagen del producto es un campo `image_url` (URL); el placeholder se muestra cuando está vacío.