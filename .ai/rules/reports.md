---
paths:
  - 'app/Http/Controllers/Reports/**'
---

# Reports

## Módulo Reportes: ventas y cajas
Módulo Reportes: `reportes.ventas` (permiso `reportes-ver`) lista ventas `status=paid` de salón/delivery/venta rápida con filtros por rango de fechas, sale_type y document_type; `reportes.cajas` (permiso `reportes-cajas-ver`) lista el historial de CashRegisterSession (apertura/cierre, montos, usuarios, fechas, ventas asociadas). Ambos bajo `/reportes` con name prefix `reportes.`.

## Reporte de ventas: filtro comprobantes invoice + export TCPDF con preview
VentasReportController filtra DocumentType solo por type='invoice' (Boleta/Factura) para el filtro "Comprobante" del reporte. Exporta PDF: reportes.ventas.preview (stream inline, se muestra en modal iframe) y reportes.ventas.export (attachment con nombre reporte_ventas_YYYY-MM-DD_HHMMSS.pdf). Vista B/N: reports/ventas/pdf.blade.php (logo de Company en escala de grises via TCPDF, header negro, tablas B/N).
