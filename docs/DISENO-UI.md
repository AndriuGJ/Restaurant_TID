# Guía de Diseño de Interfaz (UI) — Sistema Restaurante

> Propósito: documentar el **lenguaje visual** del sistema para que cualquier persona (o agente) mantenga consistencia al crear o modificar pantallas. Estilo inspirado en cadenas de restaurante modernas (tipo KFC): **cálido, contundente y con buena legibilidad**.

---

## 1. Paleta de marca (naranja / ámbar)

Los colores de marca se definen como **tokens de Tailwind v4** en `resources/css/app.css` dentro del bloque `@theme`. Úsalos con los prefijos de clase normales:

```css
--color-brand-50:  #fff4ed;
--color-brand-100: #ffe6d5;
--color-brand-200: #feccaa;
--color-brand-300: #fdac74;
--color-brand-400: #fb893c;
--color-brand-500: #f96f14;  /* principal */
--color-brand-600: #e65a08;  /* hover */
--color-brand-700: #bf4a0a;
--color-brand-800: #973a10;
--color-brand-900: #7a3111;
--color-brand-950: #421606;
```

**Reglas de uso:**
- `brand-500` = botones principales y acentos activos. `brand-600` = estado `hover`.
- Fondo de texto ligero y llamadas a la acción destacadas → gradientes/`brand`.
- **No** uses `indigo` ni otros acentos de color de marca: la marca es naranja. El `indigo` ya quedó obsoleto en el sidebar/login.

---

## 2. Tipografía

Dos familias, definidas como tokens en `@theme` y cargadas por Vite (plugin `bunny`):

- **`font-display` = `'Bebas Neue'`** → títulos grandes y de marca (p. ej. "RESTAURANTE", "Bienvenido"). Solo pesos `400`. Se ve condensado y alto.
- **`font-sans` = `'Instrument Sans'`** → texto general. Pesos `400, 500, 600, 700, 800` disponibles.

**Reglas de uso:**
- Usa `font-display tracking-wide` para encabezados de marca y logos.
- Usa `font-bold`/`font-semibold` (Instrument Sans) para títulos de página y comandos.
- Evita tamaños gigantes sin necesidad; deja que Bebas Neue aporte el impacto.

*Para añadir un peso o fuente nueva:* edita `vite.config.js` (arreglo `fonts` del plugin `bunny`) con los pesos deseados y vuelve a ejecutar `npm run build`.

---

## 3. Iconos

- **SVG inline** (estilo Heroicons "outline", `stroke="currentColor"`, `stroke-width="1.8"`, 24×24).
- **No** se usa ninguna librería de iconos externa ni Alpine.
- Guía: `class="h-5 w-5"` para iconos pequeños en botones/líneas, `h-6 w-6` para header, `h-5 w-5` en items de menú.
- El color lo toma del elemento padre (`text-*` o `fill/currentColor`).

Ejemplo patrón:

```html
<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round" d="..." />
</svg>
```

---

## 4. Layering / fondos de la aplicación

El panel (layout `app`) usa un **contraste limpio** con la marca:

- **Header**: fondo blanco, borde inferior `gray-200`, logo en tile `brand-500`, usuario con avatar circular `brand`, botón "Cerrar sesión" outline con hover `brand`.
- **Sidebar (escritorio) y drawer (móvil)**: fondo **`slate-900`/`slate-950`** oscuro (moderno, estilo sistema POS). Secciones en `slate-500` mayúsculas; items en `slate-300/400`; item activo → `bg-brand-500/15 text-brand-400`; hover → `bg-slate-800 text-white`.
- **Main content**: fondo `gray-100` (claro) para legibilidad de tablas y tarjetas.
- **Footer**: `slate-900`, texto `slate-400`, acento del nombre en `slate-300`.

> El contraste oscuro (sidebar) + claro (contenido) + acento naranja (brand) es la firma visual del sistema.

### 4.1 Layout y footer fijo al fondo
Para que el **footer siempre quede al final** de la ventana aunque el contenido sea corto, el layout `app` usa una columna flex de altura completa:
- `body` → `h-full`
- wrapper → `flex min-h-full flex-col`
- `header` → `sticky top-0 z-20`
- fila con sidebar + main → `flex flex-1` (este crece para empujar el footer al fondo)
- `footer` al final de la columna

El **sidebar de escritorio** usa `sticky top-16 h-[calc(100vh-4rem)] overflow-y-auto` (scroll propio, fijo al scrollear). El drawer móvil es `fixed inset-y-0`.

---

## 5. Botones y controles

> **Regla general de texto (importante):** en el panel (área clara) el texto va **siempre en color oscuro fijo** — `text-gray-900` (títulos/números), `text-gray-600` (subtítulos), `text-gray-500` (etiquetas/ayuda). **No** uses variantes `dark:text-white`/`dark:text-gray-*` sobre fondos claros, porque si el modo oscuro queda activo el texto se vuelve invisible. Los paneles/tarjetas se mantienen **blancos** (`bg-white`) para contrastar.

> **Regla general de redondez:** el estilo es de **bordes casi rectos**, no muy redondeados:
> - Tarjetas/paneles → `rounded-lg`
> - Contenedores y accesos directos → `rounded-md`
> - Iconos pequeños → `rounded`
> - **Evita** `rounded-2xl` / `rounded-3xl`.

- **Primario**: `bg-brand-500 text-white hover:bg-brand-600 focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 rounded-md`.
- **Secondary/outline**: `border border-gray-200 bg-white text-gray-700 hover:border-brand-300 hover:text-brand-600 rounded-md`.
- **Inputs**: `rounded-md border-gray-300 focus:border-brand-500 focus:ring-brand-500`, con icono a la izquierda y `pl-10`.
- **Tarjetas**: `rounded-lg border border-gray-200 bg-white shadow-sm`.

## 6. Vistas públicas (guest/auth)

El login usa `layouts/guest.blade.php`:
- **Panel de marca** (izquierda, `lg`): fondo `bg-brand-600` con blobs de luz `bg-white/20 blur-3xl`, textura de líneas diagonal `grayscale` y `font-display` en blanco.
- **Columna de autenticación** (derecha): `bg-gray-950` (móvil) / `bg-gray-100` (escritorio), tarjeta centrada.

## 7. Fechas

Para fechas legibles en español usa Carbon con locale explícito (evita `dUTC` y el idioma en inglés):
```blade
{{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}{{-- ejemplo: jueves, 03 de septiembre de 2026 --}}
```

---

## 8. Recordatorio de proceso

- Tras cambiar cualquier vista (Tailwind), ejecuta **`npm run build`** (o `npm run dev`).
- Tras tocar el CSS `@theme`, el build regenera las utilidades.
- No añadas CSS suelto; todo vive en `app.css` vía `@theme` o utilidades de Tailwind.