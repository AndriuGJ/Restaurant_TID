---
paths:
  - 'app/Http/Requests/Sales/**'
---

# Requests Sales

## PaySaleRequest exige bloque SUNAT por tipo de comprobante
Al cobrar un comprobante type='invoice' (boleta B o factura F), PaySaleRequest exige un SunatConfig vigente (status=active, fechas cubren hoy, used_receipts<max_receipts) cuyo document_type_id coincida con el comprobante seleccionado; si falta, error en document_type_id. Solo la factura (nomenclature F) exige además una empresa (RUC). Ambos errores se reportan sin return temprano. PosController::activeSunatConfig(int $documentTypeId) filtra igual al numerar.
