---
paths:
  - 'database/seeders/**'
---

# Seeders

## Orden de llamadas en DatabaseSeeder respeta dependencias
En DatabaseSeeder ejecutar siempre: Ubigeo → Company → SunatConfig (necesita company), y Hall/Shift/CashRegister antes de Table (necesita halls). Todos los seeders deben ser idempotentes con firstOrCreate para permitir migrate:fresh --seed repetido. Empresa usa RUC demo 20123456789.
