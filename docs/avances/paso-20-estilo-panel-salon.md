# Paso 20 — Estilo panel: texto oscuro fijo + bordes casi rectos (aplicado al módulo Salón)

Fecha: 2026-09-03

## Objetivo
Aplicar la convención visual definida en el dashboard al **módulo Salón** (CRUD de Salones y Mesas + la vista interactiva `pos.hall`) y a los partials compartidos que usa:
- **Texto siempre en oscuro fijo** (`text-gray-900/600/500`), eliminando todas las variantes `dark:text-*` que pueden hacer invisible el texto si quedara modo oscuro.
- **Bordes casi rectos**, no muy redondeados (tarjetas/paneles `rounded-md`, manteniendo `rounded-full` solo para badges).
- Quitar el acento **indigo** (obsoleto) en favor del **brand** (naranja).

## Qué se hizo

### 1. `restaurant/halls/*` (CRUD Salones)
- `index`: header texto oscuro fijo, botón "Nuevo salón" `bg-indigo-600` → `bg-brand-500`, tarjeta de tabla sin `dark:` y `rounded-lg` → `rounded-md`, cabeceras/celdas/vacío en grises fijos.
- `_form`: inputs `focus:border-indigo-500` → `focus:border-brand-500`, checkbox `text-indigo-600` → `text-brand-500`, botón guardar brand, quitar `dark:` de labels/errores.
- `create`/`edit`: título oscuro fijo, tarjeta `rounded-lg` → `rounded-md` sin `dark:`.

### 2. `restaurant/tables/*` (CRUD Mesas)
- `index`: mismo tratamiento que Salones (botón brand, tabla `rounded-md`, badges de estado sin `dark:`).
- `_form`: inputs/select `focus:border-brand-500`, botón brand, sin `dark:`.
- `create`/`edit`: título oscuro fijo, tarjeta `rounded-md`.

### 3. `pos/hall.blade.php` + `pos/_table-node.blade.php` (Salón interactivo POS)
- Textos de encabezado/leyenda/select en grises fijos (sin `dark:`); selector `focus:border-indigo-500` → `focus:border-brand-500`; canvas `rounded-lg` → `rounded-md` sin `dark:bg`; caja de error `rounded-md` sin `dark:`.
- Colores de estado de las mesas (`_table-node`): se quitaron los overrides `dark:bg-amber-500`/`dark:bg-blue-500`/`dark:bg-green-500` — quedan siempre `bg-amber-400`/`bg-blue-400`/`bg-green-400`.

### 4. Partials compartidos
- `_partials/status-badge.blade.php`: sin `dark:`.
- `_partials/row-actions.blade.php`: enlace "Editar" `text-indigo-600` → `text-brand-600`, sin `dark:text-indigo-400`.

## Reglas grabadas
- Se anotó en `.ai/rules/views.md` la convención: **texto oscuro fijo sin `dark:text-*`, paneles `bg-white`, bordes casi rectos (`rounded-lg` tarjetas / `rounded-md` contenedores / `rounded` iconos), y fechas en español con `translatedFormat`**.
- `docs/DISENO-UI.md` secciones 5 a 8 actualizadas con estas reglas (controles, vistas públicas, fechas y recordatorio de proceso).

## Verificación
- `npm run build` OK; `php artisan view:clear` aplicado.
- Suite completa: **219/219** tests pasando.

## Próximo
Aplicar el mismo criterio al resto de pantallas del panel (POS venta/checkout, cocina, inventario, clientes, cajas, reportes, configuración) para uniformar todo el sistema.