---
paths:
  - 'resources/views/**'
---

# Views

## Stack de vistas: Blade + Layouts y partials
Frontend en Blade (TailwindCSS via Vite); Livewire solo donde se requiera interacción sin recarga. Layouts: layouts/app.blade.php (panel autenticado) y layouts/guest.blade.php (login). Partials reutilizables en resources/views/_partials/ (header, sidebar, footer) incluidos desde el layout app.

## Identidad visual brand (naranja + Bebas Neue) e iconos SVG inline
Identidad visual: paleta de marca en `app.css` via `@theme` (`--color-brand-*`, naranja/ámbar estilo comida). Tipografía display `font-display` (Bebas Neue) para títulos y `font-sans` (Instrument Sans) para texto. Usar iconos SVG inline (Heroicons outline) — NO añadir librerías de iconos ni Alpine. El login usa `layouts/guest` con panel de marca y tarjeta de autenticación. Panel: header blanco + sidebar/footer oscuro slate + acento brand. **Consultar `docs/DISENO-UI.md` antes de crear/modificar vistas.**

## Texto oscuro fijo + bordes casi rectos (nada de dark:táctil)
Estilo de texto y redondez para TODO el panel: el texto va SIEMPRE en color oscuro fijo (text-gray-900/600/500) SIN variantes dark:text-* que puedan ocultarlo sobre fondos claros; los paneles/tarjetas se mantienen en blanco (bg-white) para contraste. Bordes casi rectos, no muy redondeados: tarjetas `rounded-lg`, accesos directos y contenedores `rounded-md`, iconos `rounded`. Evitar `rounded-2xl`/`rounded-3xl`. Fechas legibles en español con `now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y')`.
