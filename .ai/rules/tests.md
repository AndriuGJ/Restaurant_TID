---
paths:
  - tests/Pest.php
---

# Tests

## RefreshDatabase habilitado en tests Feature
tests/Feature usa RefreshDatabase (descomentado en Pest.php). Los tests de autenticacion y CRUD no deben re-habilitarlo ni asumir seeders; crear permisos/roles dentro del test (beforeEach) cuando se pruebe acceso por permiso.
