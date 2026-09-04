---
paths:
  - 'resources/views/layouts/**'
---

# Layouts

## Footer al fondo con layout de columna flex-1
Layout app: para que el footer siempre quede al fondo aunque el contenido sea corto, la estructura es columna flex de altura completa — `body.h-full`, wrapper `div.flex.min-h-full.flex-col`, header sticky top-0, la fila con sidebar+main usa `flex.flex-1`, y el footer va al final (shrink-0). El sidebar de escritorio es `sticky top-16 h-[calc(100vh-4rem)] overflow-y-auto` para scroll interno propio.
