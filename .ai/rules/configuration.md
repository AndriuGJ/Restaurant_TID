---
paths:
  - 'app/Http/Controllers/Configuration/**'
---

# Configuration

## No sobrescribir sol_password al actualizar Company
En CompanyController@update, si sol_password llega vacío se hace unset($data['sol_password']) para conservar el valor en BD. Los Form Requests la permiten nullable.
