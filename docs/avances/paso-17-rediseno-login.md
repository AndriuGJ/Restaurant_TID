# Paso 17 — Rediseño del login y marca (estilo moderno tipo fast-food)

Fecha: 2026-09-03

## Objetivo
Empezar una rediseño visual más atractivo del sistema, inspirado en cadenas de restaurantes modernas (KFC y similares): iconos modernos, tipografía display más llamativa y una marca visual clara. Este paso se enfoca en la **pantalla de login**.

## Qué se hizo
- **Sistema de color de marca**: se añadió una paleta `brand` (naranja/ámbar cálidos, estilo comida) en `resources/css/app.css` vía Tailwind v4 `@theme` (`--color-brand-50..950`).
- **Tipografía display**: nueva fuente **Bebas Neue** (400) y pesos extra de **Instrument Sans (700/800)** añadidos a `vite.config.js` (plugin `@laravel/vite-plugin/fonts` desde Bunny Fonts). Se define `--font-display: 'Bebas Neue'`.
- **`layouts/guest.blade.php`** rediseñado en dos columnas:
  - Panel de **marca** (izquierda, solo escritorio `lg`): fondo degradado `brand`, formas de luz difusa, textura punteada, logo, eslogan y dos puntos destacados.
  - Columna de **autenticación** (derecha): fondo claro, centrada, con nota al pie de copyright.
- **`auth/login.blade.php`** rediseñado:
  - Encabezado de marca destacado en escritorio ("Bienvenido") y una fila compacta en móvil.
  - Tarjeta con esquinas redondeadas y sombra suave.
  - Campos con **iconos SVG inline** (usuario y candado) y placeholder.
  - Botón degradado `brand` con icono de flecha y efecto hover.
  - Mensaje de error de login con icono de advertencia.

## Iconos
Se usan **SVG inline** (Heroicons outline), coherente con el proyecto — no se añade ninguna librería de iconos ni dependencia nueva (no hay Alpine).

## Verificación
- `npm run build` → nuevas fuentes (Bebas Neue + Instrument Sans 700) y CSS de marca compilados.
- `http://localhost:8000/login` responde **200** y renderiza las clases `font-display` / `bg-brand-500`.
- Suite completa: **214/214** tests pasando (`LoginTest` verifica que la página cargue y el redireccionamiento, sin aserciones de texto de la vista).

## Próximo (fuera de este paso)
Aplicar la misma identidad visual al **layout app** (header, sidebar e iconos) para que toda la aplicación luzca el nuevo estilo.