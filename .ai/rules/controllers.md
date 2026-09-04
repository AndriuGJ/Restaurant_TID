---
paths:
  - 'app/Http/Controllers/**'
---

# Controllers

## Controllers y Form Requests por módulo
Los controllers van en subcarpetas por módulo (app/Http/Controllers/<Modulo>/). Toda validación debe hacerse en Form Requests dedicados (app/Http/Requests/<Modulo>/StoreXRequest y UpdateXRequest), el método del controller recibe el Request tipado y se valida por inyección.
