# Avances del Sistema de Restaurante

## Paso 2 — Reorganización de modelos por módulo (Fecha: 2026-09-02)

### Realizado
Se movieron todos los modelos a carpetas por módulo (según la agrupación de la documentación `migraciones_y_modelos_laravel.md`), actualizando los namespaces (`App\Models\<Modulo>\<Modelo>`) y sus relaciones Eloquent para que apunten a las clases correctas con los `use` necesarios.

**Estructura final en `app/Models/`:**
- `Configuration/`: `Ubigeo`, `Timezone`, `Currency`, `DocumentType`, `PaymentMethod`, `Company`, `SunatConfig`.
- `Restaurant/`: `Hall`, `Table`, `Shift`, `CashRegister`, `DeliveryProvider`, `CashRegisterSession`.
- `Customers/`: `Customer`, `CompanyClient`.
- `Inventory/`: `PurchaseCategory`, `ProductCategory`, `Supplier`, `Product`, `ProductIngredient`.
- `Purchases/`: `Purchase`, `PurchaseDetail`.
- `Sales/`: `Sale`, `SaleTable`, `SaleDetail`, `SalePayment`.
- `Kardex/`: `KardexMovement`.
- `User.php`: se mantiene en `App\Models\User` (usuarios/autenticación).

### Notas técnicas
- Se ejecutó `composer dump-autoload` para regenerar el autoload con los nuevos namespaces.
- Se verificó con tinker que todas las clases cargan y que **todas las relaciones Eloquent** de los 16 modelos que tienen relaciones resuelven sin errores.
- Se corrigió un `use` redundante en `Company.php` (Pint: `no_unused_imports`).
- Tests de la app pasan (2 tests OK).

### Pendiente / Siguiente paso
- ~~Instalar Spatie~~ (hecho en Paso 3).

## Paso 3 — Spatie Roles y Permisos (Fecha: 2026-09-02)

### Realizado
1. Instalado **spatie/laravel-permission** `^8.3` con Composer.
2. Publicada su configuración (`config/permission.php`) y su migración `create_permission_tables` (roles, permissions, model_has_roles, role_has_permissions y model_has_permissions).
3. Ejecutada la migración de Spatie (`php artisan migrate`) sin errores.
4. Agregado el trait `HasRoles` al modelo `App\Models\User`.
5. Creado seeder **`RolePermissionSeeder`** con:
   - **17 permisos** agrupados por módulo (configuración, POS, clientes, cajas, inventario).
   - **4 roles** (`administrador`, `chef`, `mozo`, `contabilidad`) con sus permisos según la tabla de roles de la sección 1 del README.
   - Utiliza `Permission::firstOrCreate()` y `Role::firstOrCreate()` (idempotente) + `syncPermissions()`.
6. Actualizado el **`DatabaseSeeder`** para que llame a `RolePermissionSeeder` y crear un usuario de prueba con el esquema actual de User.
7. Corregido el **`UserFactory`** al nuevo esquema (dni, first_name, last_name, username, cargo, status) ya que la original generaba `name`/`email_verified_at` que ya no existen.

### Verificación
- `db:seed` ejecutado sin errores; roles y 17 permisos insertados.
- Tinker: un `User` con rol `mozo` reconoce su rol, tiene permiso `pos-ventas`, y NO tiene `inventario-ver` (scoping correcto).
- Pint aplicado (`ordered_traits` en User.php) y tests pasan (2 OK).

### Nota
- El permiso/rol `card_surcharge` y otros detalles de SUNAT se manejarán en sus módulos correspondientes.
- El middleware `role:`/`can:` se aplicará cuando se creen las rutas/controladores por módulo.

### Pendiente / Siguiente paso
- Seeders de catálogos base (DocumentType, PaymentMethod, Currency, Timezone, Ubigeo, Company, SunatConfig, ProductCategory, PurchaseCategory, Hall/Table, Shift, CashRegister) — paso 3 del orden del README.

