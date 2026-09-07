---
paths:
  - 'resources/views/**'
  - 'resources/views/**/*.blade.php'
---

# Views

## Stack de vistas: Blade + Layouts y partials
Frontend en Blade (TailwindCSS via Vite); Livewire solo donde se requiera interacción sin recarga. Layouts: layouts/app.blade.php (panel autenticado) y layouts/guest.blade.php (login). Partials reutilizables en resources/views/_partials/ (header, sidebar, footer) incluidos desde el layout app.

## Identidad visual brand (naranja + Bebas Neue) e iconos SVG inline
Identidad visual: paleta de marca en `app.css` via `@theme` (`--color-brand-*`, naranja/ámbar estilo comida). Tipografía display `font-display` (Bebas Neue) para títulos y `font-sans` (Instrument Sans) para texto. Usar iconos SVG inline (Heroicons outline) — NO añadir librerías de iconos ni Alpine. El login usa `layouts/guest` con panel de marca y tarjeta de autenticación. Panel: header/sidebar/footer en `slate-900` (gris oscuro) + borde `slate-800` + acento brand; contenido claro en `gray-100`. **Consultar `docs/DISENO-UI.md` antes de crear/modificar vistas.**

## Texto oscuro fijo + bordes casi rectos (nada de dark:táctil)
Estilo de texto y redondez para TODO el panel: el texto va SIEMPRE en color oscuro fijo (text-gray-900/600/500) SIN variantes dark:text-* que puedan ocultarlo sobre fondos claros; los paneles/tarjetas se mantienen en blanco (bg-white) para contraste. Bordes casi rectos, no muy redondeados: tarjetas `rounded-lg`, accesos directos y contenedores `rounded-md`, iconos `rounded`. Evitar `rounded-2xl`/`rounded-3xl`. Fechas legibles en español con `now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y')`.

## Estilo visual: sin dark mode, colores brand
Texto siempre oscuro fijo: `text-gray-900`, `text-gray-600`, `text-gray-500`, `text-gray-700`. NUNCA usar variantes `dark:`. Botones primarios: `bg-brand-500 hover:bg-brand-600 text-white`. Inputs focus: `focus:border-brand-500 focus:ring-brand-500`. Iconos: `rounded-md`. Contenedores/tarjetas: `rounded-lg`. Fuentes: Bebas Neue (display) + Instrument Sans (body).
