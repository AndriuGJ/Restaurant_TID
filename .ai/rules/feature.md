---
paths:
  - 'tests/Feature/**'
---

# Feature

## Helpers en tests Pest deben tener nombres unicos
Pest incluye todos los archivos de tests en el mismo proceso, por lo que las funciones globales de ayuda NO deben repetirse entre archivos (colisionan con "Cannot redeclare"). Usar nombres unicos por archivo (ej: createShiftAdmin, createTableAdmin) o closures. Para probar acceso por permiso crear roles/permisos en beforeEach y asignar el rol al usuario.
