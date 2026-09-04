# Paso 9 — Módulo Clientes (Customer, CompanyClient)

Fecha: 2026-09-02

## Objetivo
Implementar el módulo **Clientes** según la sección 7.2 del README: CRUD simple de `Customer` (persona natural) y `CompanyClient` (empresa), ambos con `status` activo por defecto. Ambos se conectarán a `Sale` por relación polimórfica `clientable` (se dejará lista la relación; la venta queda para el módulo POS).

## Qué se hizo

### 1. Controllers (`app/Http/Controllers/Customers/`)
- `CustomerController`: `index`, `create`, `store`, `edit`, `update`, `destroy`. El index hace eager loading de `documentType`. No se elimina un cliente con ventas. Los selects de tipo de documento solo muestran los de `type = 'identification'` (DNI, RUC, etc.).
- `CompanyClientController`: ídem para empresa. RUC único. No se elimina una empresa con ventas.

### 2. Form Requests (`app/Http/Requests/Customers/`)
- `StoreCustomerRequest` / `UpdateCustomerRequest`: validan `name` (requerido), `email` (formato), `document_type_id` (existe), número/documento opcional, `status` booleano.
- `StoreCompanyClientRequest` / `UpdateCompanyClientRequest`: validan `ruc` (requerido y único, `ignore` en update), `social_reason` (requerido), `document_type_id` (existe), `status` booleano.

### 3. Rutas (`routes/web.php`)
Subgrupo `Route::prefix('clientes')->name('customers.')`:
- `/` → `customers.index`, `/create`, `/{customer}/edit`, `/{customer}` (PUT/DELETE) — permisos `clientes-ver` (index) y `clientes-gestionar` (mutaciones).
- `/empresas` → `customers.companies.*` para CompanyClient (mismos permisos).
- **Importante**: `/empresas/...` se registra antes de `/{customer}/...` para evitar que el parámetro capture "empresas".

### 4. Vistas (`resources/views/customers/` y `resources/views/company-clients/`)
- `index`, `create`, `edit`, `_form` para cada entidad. Incluyen select de tipo de documento (identificación), campos propios y checkbox de estado activo.

### 5. Sidebar (`resources/views/_partials/sidebar.blade.php`)
Nueva sección **"Clientes"** (permiso `clientes-ver`) con el grupo "Catálogo" e ítems Clientes y Empresas.

### 6. Factories (`database/factories/Customers/`)
- `CustomerFactory` y `CompanyClientFactory`. Usan `DocumentType::factory()->identification()` (nuevo estado en `DocumentTypeFactory`) para que `document_type_id` siempre referencie un tipo de identificación estable en tests (sin depender de seeders).

### 7. Tests Feature (`tests/Feature/Customers/CustomerTest.php` y `CompanyClientTest.php`)
Por entidad cubren: acceso protegido (guest → login), sin permiso → 403, listar, crear, validaciones (nombre requerido, email, RUC único), actualizar y eliminar. Helpers únicos (`createCustomerAdmin`, `createCompanyClientAdmin`).

## Assets
- `npm run build` ejecutado.

## Resultado
- Suite completa: **101 tests, 101 passed** (15 nuevos del módulo Clientes).
- `vendor/bin/pint` aplicado y limpio.
- 12 rutas registradas bajo `/clientes`.

## Pendiente
- Módulo de **Inventario** (`Supplier`, `PurchaseCategory`, `ProductCategory`, `Purchase`, `Product`, `Kardex`) — siguiente paso del README.