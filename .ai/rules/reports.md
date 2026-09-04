---
paths:
  - 'app/Http/Controllers/Reports/**'
---

# Reports

## Módulo Reportes: ventas y cajas
Módulo Reportes: `reportes.ventas` (permiso `reportes-ver`) lista ventas `status=paid` de salón/delivery/venta rápida con filtros por rango de fechas, sale_type y document_type; `reportes.cajas` (permiso `reportes-cajas-ver`) lista el historial de CashRegisterSession (apertura/cierre, montos, usuarios, fechas, ventas asociadas). Ambos bajo `/reportes` con name prefix `reportes.`.
