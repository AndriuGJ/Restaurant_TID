# Paso 22 — Empresa única: sin crear/borrar, detalle + icono para comprobantes

Fecha: 2026-09-03

## Objetivo
En **Configuración → Sistema → Empresas**, el sistema pertenece a **una sola empresa**, así que se eliminó la posibilidad de **crear** o **borrar** empresas (solo se conservan **ver datos** y **editar**). Además se agregó un campo para **subir el icono/foto de la empresa**, el cual se muestra en los comprobantes y facturas electrónicas.

## Qué se hizo

### Rutas (`routes/web.php`)
Se quitaron las rutas `companies.create`, `companies.store` y `companies.destroy`. Quedan:
- `GET  configuracion/companies` → `index` (`configuracion-ver`).
- `GET  configuracion/companies/{company}/edit` → `edit` (`configuracion-editar`).
- `PUT  configuracion/companies/{company}` → `update` (`configuracion-editar`).

Las rutas de **Clientes → Empresas** (`customers.companies.*`) se mantienen intactas.

### Controlador `CompanyController`
- Eliminados `create`, `store` y `destroy`.
- `index` ahora muestra la **empresa única** (la primera registrada) en una **tarjeta de detalle** en vez de una tabla con botones.
- `update` maneja la **subida del logo**: si viene el archivo `logo`, se guarda con `Storage::disk('public')` en `logos/` y se persiste esa ruta en `companies.logo`; si no viene archivo, conserva el actual. Al subir un **logo nuevo se elimina primero el archivo anterior** de la carpeta (no se acumulan.)

### Form Request
- `UpdateCompanyRequest`: `logo` pasa de `nullable|string` a `nullable|image|mimes:jpeg,png,webp|max:2048`, con mensajes en español.
- Se eliminó `StoreCompanyRequest` (ya no existe el alta).

### Vistas
- **`index.blade.php`**: rediseñada como tarjeta de detalle de la empresa única — encabezado con icono/logo, razón social, RUC y detalles (teléfono, direcciones, ubigeo, usuario SOL, estado del icono). Botón **"Editar empresa"** gated por `configuracion-editar`. Sin `dark:`, bordes casi rectos, acento brand.
- **`_form.blade.php`**: rediseñada con la misma convención, agrupada en secciones (Datos principales, Dirección, SUNAT y certificado). Agrega el bloque **"Icono para comprobantes / facturación"** con **vista previa**, botón "Subir icono" (input `type="file"` oculto con label en brand) y nota de tamaño (máx. 2 MB). Si no se sube nada, se conserva el actual.
- **`edit.blade.php`**: `enctype="multipart/form-data"` y script (vía `@push('scripts')`) que muestra la **vista previa** del archivo seleccionado con `FileReader`.
- Se eliminó **`create.blade.php`**.

### Comprobante electrónico (`pos/receipt.blade.php`)
El ticket 80 mm muestra ahora el **logo de la empresa** (`asset('storage/'.$logo)`) centrado sobre la razón social, de modo que el icono subido se usa en boletas/facturas.

### Almacenamiento
- Se creó el enlace `public/storage` (`php artisan storage:link`) para exponer los logos.

## Verificación
- `npm run build` OK; `php artisan view:clear` aplicado; `storage:link` activo.
- `CompanyTest` actualizado: se quitaron los tests de store/delete, se agregó el chequeo de que ya **no existen** las rutas de create/store/destroy, y tres tests nuevos para el logo (rechaza archivo >2 MB, conserva logo si no se sube archivo, y **elimina el logo anterior al reemplazarlo**). **8/8 OK**.
- Suite completa: **220/220** tests pasando.

## Próximo
Aplicar la misma convención visual (sin `dark:`, bordes casi rectos, brand) al resto de pantallas del panel: inventario, clientes, cajas, reportes y usuarios/roles.