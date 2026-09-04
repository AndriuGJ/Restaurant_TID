# Avances del Sistema de Restaurante

## Paso 1 — Base de datos y modelos (Fecha: 2026-09-02)

### Realizado
1. **Configuración de entorno**
   - Cambiado `DB_CONNECTION` a `mysql` en `.env` (antes SQLite).
   - Creada la base de datos `restaurante_sistema` en MySQL (XAMPP, user `root`).

2. **Creación de 28 modelos + migraciones** (vía `make:model -m`), en orden de dependencias:
   - Módulo Configuración: `Ubigeo`, `Timezone`, `Currency`, `DocumentType`, `PaymentMethod`, `Company`, `SunatConfig`.
   - Módulo Usuarios: editado `User` y su migración por defecto (agregados `dni`, `first_name`, `last_name`, `username`, `cargo`, `status`; removidos `name` y `email_verified_at`).
   - Módulo Restaurante: `Hall`, `Table`, `Shift`, `CashRegister`, `DeliveryProvider`, `CashRegisterSession`.
   - Módulo Clientes: `Customer`, `CompanyClient`.
   - Módulo Inventario: `PurchaseCategory`, `ProductCategory`, `Supplier`, `Product`, `ProductIngredient`.
   - Módulo Compras: `Purchase`, `PurchaseDetail`.
   - Módulo Ventas: `Sale`, `SaleTable`, `SaleDetail`, `SalePayment`.
   - Módulo Kardex: `KardexMovement`.

3. **Copia de código** de cada migración y modelo desde `docs/migraciones_y_modelos_laravel.md` a los archivos generados.

4. **Migraciones ejecutadas correctamente** (`php artisan migrate`) — las 27 tablas nuevas + las 3 por defecto (users, cache, jobs) sin errores.

5. **Pint ejecutado** — sin errores de formato.

### Pendiente / Siguiente paso
- Según el orden recomendado del README (sección 8):
  1. ✅ Migraciones y modelos (hecho).
  2. ⏳ Spatie roles/permisos + middleware (`composer require spatie/laravel-permission`, seeder `RolePermissionSeeder`).
  3. ⏳ Seeders de catálogos base (DocumentType, PaymentMethod, Currency, Timezone, Ubigeo, Company, SunatConfig, ProductCategory, PurchaseCategory, Hall/Table, Shift, CashRegister).
  4. ⏳ CRUD de Configuración → Restaurante.
