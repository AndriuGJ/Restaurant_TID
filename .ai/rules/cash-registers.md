---
paths:
  - 'app/Http/Controllers/CashRegisters/**'
---

# Cash Registers

## Apertura de caja sugiere monto de cierre anterior
La apertura de una sesión puede sugerir el closing_amount de la última sesión cerrada de esa caja (regla del documento). Se expone vía CashRegisterSessionController::suggestedOpeningAmount(register) y se muestra como ayuda en la vista create.

## Apertura de caja con carry-forward del monto de cierre
Al abrir caja la vista `cash-registers.create` pre-llena `opening_amount` con el `closing_amount` de la última sesión CERRAAda de la caja (carry-forward) y es editable (JS actualiza el monto al cambiar de caja). El cierre guarda `closing_amount`, `user_closing_id` y `closed_at`.
