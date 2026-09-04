---
paths:
  - 'app/Http/Controllers/Users/**'
---

# Users

## Módulo Usuarios: CRUD + roles y permisos
Módulo Usuarios: `users.index` (permiso `usuarios-ver`), CRUD de usuarios + asignación de roles (`usuarios-gestionar`), y gestión de roles/permisos (`roles-gestionar`) bajo `usuarios/*`. Todo usuario requiere al menos un rol (`roles` es requerido). La contraseña en update solo se cambia si se llena. No se permite eliminar al propio usuario, ni el rol `administrador`. Al crear roles, el nombre se guarda como slug.
