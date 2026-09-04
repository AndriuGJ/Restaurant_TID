---
paths:
  - resources/views/_partials/sidebar.blade.php
---

# Partials

## Sidebar data-driven escalable con grupos colapsables
El sidebar (y su drawer móvil) es data-driven: se define un array $menu (secciones con 'permission' y 'groups' con 'children' con label+route) en `_partials/sidebar-menu.blade.php` y se renderiza con bucles. Cada grupo se colapsa con <details>/<summary> nativo (sin JS). Para agregar un módulo nuevo solo se añade una sección/group al array.

## Estilo oscuro de marca
El sidebar de escritorio (`_partials/sidebar.blade.php`) y el drawer móvil (`layouts/app.blade.php`) usan fondo oscuro **slate-900/slate-950**, borde `slate-800`, secciones `text-slate-500`, items `text-slate-300/400`, hover `bg-slate-800 text-white`, y activo `bg-brand-500/15 text-brand-400` (acento naranja brand). NO usar fondos blancos ni acentos indigo en sidebar/footer. Ver guía `docs/DISENO-UI.md`.
