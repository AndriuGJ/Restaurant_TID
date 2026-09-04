# Paso 7 — CRUDs de Configuración → Sistema (DocumentType, PaymentMethod, Company, SunatConfig)

Fecha: 2026-09-02

## Objetivo
Completar el módulo **Configuración → Sistema** con los CRUDs de tipografías de documento, medios/forma de pago, empresa (con su configuración fiscal/RUC/SUNAT) y la configuración de vigencia de comprobantes SUNAT. Se siguen las mismas convenciones del proyecto (controladores por módulo, Form Requests, vistas Blade + Tailwind, permisos finos).

## Qué se hizo

### 1. CRUD de DocumentType (Tipos de documento) — `Configuration\DocumentTypeController`
- Campos: `name`, `nomenclature`, `character_limit`, `type` (`identification`/`invoice`), `status` (booleano).
- Form Requests `Store/Update` con validación de nombre único, `type` en el enum permitido.
- Vistas en `configuration/document-types/` (index, create, edit, _form).

### 2. CRUD de PaymentMethod (Medios de pago) — `Configuration\PaymentMethodController`
- Campos: `name`, `type` (`cash`/`card`/`digital`), `status` (booleano).
- No se elimina un medio de pago que tenga `salePayments` asociados.
- Vistas en `configuration/payment-methods/`.

### 3. CRUD de Company (Empresa) — `Configuration\CompanyController`
- Campos: nombre, RUC (único), razón social, teléfono, direcciones comercial/fiscal, logotipo y datos de acceso a SUNAT (`sol_user`, `sol_password` — este último `$hidden`).
- En **update**, la `sol_password` vacía se conserva (no se sobreescribe): el controller hace `unset($data['sol_password'])` si viene vacío.
- No se elimina una empresa que tenga `sunatConfigs` asociados.
- Select de Ubigeo (244 ubigeos sembrados) para la dirección comercial.
- Vistas en `configuration/companies/`.

### 4. CRUD de SunatConfig (Configuración SUNAT) — `Configuration\SunatConfigController`
- Campos: `company_id` (FK), `start_date`, `end_date` (fin debe ser `after_or_equal` al inicio), `status` (`active`/`inactive`/`expired`), `max_receipts` (tope), `used_receipts` (usados) y `card_surcharge_percentage` (recargo por tarjeta, máx 100%).
- La vista index muestra empresa, vigencia, estado (badge de color) y tope/uso de comprobantes.
- Vistas en `configuration/sunat-configs/`.

### 5. Rutas (`routes/web.php`)
- Nuevo subgrupo `Route::prefix('configuracion')->name('configuration.')` con las rutas explícitas de `document-types`, `payment-methods`, `companies` y `sunat-configs`, todas con permisos finos: `configuracion-ver` en index y `configuracion-editar` en create/store/edit/update/destroy.

### 6. Sidebar (`resources/views/_partials/sidebar.blade.php`)
- Se agregó el grupo **"Sistema"** dentro de la sección Configuración, con enlaces a los 4 nuevos CRUDs.

### 7. Factories (`database/factories/Configuration/`)
- `DocumentTypeFactory`, `PaymentMethodFactory`, `CompanyFactory` (crea un `Ubigeo` asociado y `sol_password` de prueba), `SunatConfigFactory` (crea su `Company`), más `UbigeoFactory`.

### 8. Tests Feature (`tests/Feature/Configuration/`)
- `DocumentTypeTest`, `PaymentMethodTest`, `CompanyTest` y `SunatConfigTest`. Por entidad cubren: guest redirigido a login, usuario sin permiso → 403, listar, crear, validaciones (nombre/RUC únicos, enum de tipo, fecha fin >= inicio), actualizar y eliminar.
- `CompanyTest` verifica que la `sol_password` se conserva al actualizar dejándola en blanco.
- Helpers de creación únicos por archivo (`createDocumentTypeAdmin`, `createCompanyAdmin`, etc.) para evitar la colisión de funciones globales de Pest.

## Assets
- `npm run build` ejecutado (Tailwind procesa las nuevas clases de las vistas).

## Resultado
- Suite completa: **77 tests, 77 passed** (29 nuevos de Configuration).
- `vendor/bin/pint` aplicado y limpio en los archivos nuevos del módulo.
- Rutas registradas y visibles en `php artisan route:list` (24 rutas nuevas bajo `configuracion`).

## Pendiente
- Siguiente módulo del README (sección 9 en adelante): ventas / operación diaria del restaurante.