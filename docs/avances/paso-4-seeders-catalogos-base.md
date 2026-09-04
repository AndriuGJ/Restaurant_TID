# Avances del Sistema de Restaurante

## Paso 4 — Seeders de catálogos base (Perú) (Fecha: 2026-09-02)

### Realizado
Se crearon e integraron todos los seeders de catálogos base necesarios antes de usar el sistema (según README sección 6). Todos orientados a **Perú** (SUNAT, Sol, ubigeos, Yape/Plin).

**Seeders creados:**
| Seeder | Datos |
|--------|-------|
| `UbigeoSeeder` | 244 registros: 24 departamentos del Perú + provincias y distritos (Lima completo, capital provincial de cada departamento). Códigos ubigeo oficiales de 6 dígitos. |
| `TimezoneSeeder` | Lima (-05:00), Bogotá, Quito. |
| `CurrencySeeder` | Sol Peruano (S/, `is_default=true`), Dólar Americano. |
| `DocumentTypeSeeder` | identification: DNI (8), RUC (11), Carnet de Extranjería (12); invoice: Boleta (B), Factura (F). |
| `PaymentMethodSeeder` | Efectivo (cash), Tarjeta (card), Yape (digital), Plin (digital). |
| `CompanySeeder` | Empresa demo `Restaurante Demo S.A.C.` (RUC 20123456789) con dirección en Miraflores/Lima y datos SOL de ejemplo. |
| `SunatConfigSeeder` | Config SUNAT vigente (1 año calendario, `status=active`, max 10000 comprobantes, surcharge 0). |
| `ProductCategorySeeder` | Bebidas, Postres, Comida Caliente, Comida Criolla, Entradas, Sopas, Ceviches, Parrillas. |
| `PurchaseCategorySeeder` | Materia Prima, Insumo. |
| `ShiftSeeder` | Mañana, Tarde, Noche. |
| `HallSeeder` + `TableSeeder` | 3 salones + 18 mesas (6 por salón, formas variadas). |
| `CashRegisterSeeder` | Caja Principal, Caja Delivery. |

**`DatabaseSeeder` reestructurado:** orden de llamadas respetando dependencias (ubigeo → company → sunat → halls → tables) y crea un usuario **admin** con rol `administrador`.

### Verificación
- `migrate:fresh --seed` ejecutado sin errores.
- Datos insertados: 5 document_types, 4 payment_methods, 2 currencies, 3 timezones, 244 ubigeos, 1 company, 1 sunat_config, 8 product_categories, 2 purchase_categories, 3 shifts, 3 halls, 18 tables, 2 cash_registers, 4 roles, 1 user (admin con rol administrador).
- Empresa con SUNAT vigente y dirección Lima correcta.
- Pint y tests pasan (2 OK).

### Pendiente / Siguiente paso
- CRUD de Configuración → Restaurante (`Hall`, `Table`, `Shift`, `CashRegister`, `DeliveryProvider`) — paso 4 del orden del README. Requiere definir el front-end (probablemente Livewire/Blade) y las rutas/controladores con middleware de permisos.
