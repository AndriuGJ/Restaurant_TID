---
paths:
  - 'resources/views/dashboard/**'
---

# Dashboard

## Dashboard: stats + accesos rápidos + componente de iconos
Dashboard: el controlador (`DashboardController`) pasa `$stats` (ventas hoy/total, en cocina, stock bajo, estado de caja abierta) y `$shortcuts` (accesos rápidos filtrados por permiso del usuario). Los iconos de accesos rápidos se renderizan con el componente lámina `resources/views/components/dashboard-icon.blade.php` (`<x-dashboard-icon icon="hall" class="...">`), que mapea claves a rutas SVG Heroicons.
