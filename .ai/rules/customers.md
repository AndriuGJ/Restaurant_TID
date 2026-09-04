---
paths:
  - 'app/Http/Controllers/Customers/**'
---

# Customers

## Redirects de CompanyClientController usan customers.companies.*
Los redirects de CompanyClientController usan la ruta customers.companies.index (NO company-clients.index). El prefijo de Clientes es /clientes con name() group 'customers.' y subgrupo 'empresas' para company-clients → customers.companies.*. No se elimina un cliente/empresa con ventas.
