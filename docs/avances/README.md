# Avances — Sistema de Restaurante

Registro cronológico del avance del proyecto. Cada paso tiene su archivo detallado. Consulta el último paso para saber el estado actual.

| Paso | Descripción | Fecha | Estado |
|------|-------------|-------|--------|
| [1](paso-1-migraciones-y-modelos.md) | Migraciones y modelos (28 tablas) + base MySQL | 2026-09-02 | ✅ |
| [2](paso-2-modelos-por-modulo.md) | Modelos organizados por módulo en subcarpetas | 2026-09-02 | ✅ |
| [3](paso-2-modelos-por-modulo.md#paso-3--spatie-roles-y-permisos) | Spatie roles y permisos + seeder | 2026-09-02 | ✅ |
| [4](paso-4-seeders-catalogos-base.md) | Seeders de catálogos base orientados a Perú | 2026-09-02 | ✅ |
| [5](paso-5-auth-y-crud-hall.md) | Layouts + auth manual + CRUD de Hall (plantilla) | 2026-09-02 | ✅ |
| [6](paso-6-cruds-restaurante.md) | Sidebar escalable + CRUD de Mesas, Turnos, Cajas y Delivery | 2026-09-02 | ✅ |
| [7](paso-7-configuracion-sistema.md) | CRUD de Configuración → Sistema: DocumentType, PaymentMethod, Company, SunatConfig | 2026-09-02 | ✅ |
| [8](paso-8-cajas.md) | Módulo Cajas: apertura/cierre de sesiones de caja | 2026-09-02 | ✅ |
| [9](paso-9-clientes.md) | Módulo Clientes: Customer y CompanyClient | 2026-09-02 | ✅ |
| [10](paso-10-inventario.md) | Módulo Inventario — Fases 1-3: Proveedores, Categorías, Productos, Compras y Kardex | 2026-09-02 | ✅ |
| [11](paso-11-pos.md) | Módulo POS — Avance 1: ventas en salón, cobro y preparación en cocina | 2026-09-02 | ✅ |
| [12](paso-12-pos-delivery-rapida.md) | Módulo POS — Avance 2: Delivery y Venta rápida | 2026-09-02 | ✅ |
| [13](paso-13-factura-electronica.md) | Módulo POS — Factura electrónica: SunatConfig + serie/número + greenter | 2026-09-02 | ✅ |
| [14](paso-14-reportes-y-caja-pos.md) | Reportes (Ventas y Cajas) + POS requiere caja abierta | 2026-09-02 | ✅ |
| [15](paso-15-usuarios-roles-y-fotos-pos.md) | Módulo Usuarios (CRUD + roles/permisos) + fotos de platos en POS | 2026-09-02 | ✅ |
| [16](paso-16-boleta-factura-y-vuelto.md) | POS: Boleta/Factura diferenciadas + cálculo de vuelto y ticket 80 mm | 2026-09-02 | ✅ |
| [17](paso-17-rediseno-login.md) | Rediseño visual: marca brand (naranja), tipografía Bebas Neue + iconos modernos en login | 2026-09-03 | ✅ |
| [18](paso-18-diseno-header-sidebar-footer.md) | Identidad visual en Header, Sidebar (oscuro) y Footer + guía `docs/DISENO-UI.md` | 2026-09-03 | ✅ |
| [19](paso-19-dashboard-y-footer-fijo.md) | Dashboard con métricas + accesos rápidos + footer fijo al fondo | 2026-09-03 | ✅ |
| [20](paso-20-estilo-panel-salon.md) | Estilo panel: texto oscuro fijo + bordes casi rectos aplicados al módulo Salón + reglas en `.ai/rules` y `DISENO-UI.md` | 2026-09-03 | ✅ |
| [21](paso-21-estilo-panel-pos.md) | Estilo panel aplicado al módulo POS (pedido, cobro, cocina, ticket 80 mm, caja no abierta) | 2026-09-03 | ✅ |
| [22](paso-22-empresa-unica-y-logo.md) | Empresa única (sin crear/borrar) + subida de icono usado en comprobantes/facturas | 2026-09-03 | ✅ |
| [23](paso-23-login-fondo-logo.md) | Login: imagen de fondo con overlay oscuro, bordes más rectos y logo del restaurante | 2026-09-03 | ✅ |

## Estado actual
- Base de datos `restaurante_sistema` (MySQL) creada y migrada (28 tablas + permission tables).
- Modelos en `app/Models/<Módulo>/`.
- Roles y permisos con Spatie funcionando (4 roles, 17 permisos).
- Catálogos base sembrados (contexto Perú): empresa + SUNAT, moneda Sol, DNI/RUC/Boleta/Factura, medios de pago, ubigeos, categorías, turnos, salones/mesas y cajas.
- Usuario **admin** con rol `administrador` (admin@example.com).
- Panel funcional: layouts `app`/`guest`, partials, login manual (email o username), logout, dashboard y sidebar **escalable** (menú colapsable por módulos con `<details>`).
- **Módulo Configuración → Restaurante completo**: CRUDs de Salones, Mesas, Turnos, Cajas y Proveedores de Delivery, con permisos finos (ver/editar) y tests (48 tests en total).
- **Módulo Configuración → Sistema completo**: CRUDs de Tipos de documento, Medios de pago, Empresas y Configuración SUNAT, con permisos finos y tests (suite completa: **77 tests**).
- **Módulo Cajas completo**: apertura y cierre de sesiones de caja (`CashRegisterSession`), con sugerencia del monto de cierre anterior y validación de no doble apertura (suite completa: **86 tests**).
- **Módulo Clientes completo**: CRUD de `Customer` (persona natural) y `CompanyClient` (empresa), con relación polimórfica `clientable` lista para `Sale` (suite completa: **101 tests**).
- **Módulo Inventario — Fase 1**: CRUDs de Proveedores (`Supplier`), Categorías de compra (`PurchaseCategory`) y Categorías de producto (`ProductCategory`), con permisos `inventario-ver`, bloqueo de borrado con registros relacionados, factory y tests (suite completa: **122 tests**). En Fase 2 se completa Productos; en Fase 3 Compras + Kardex (stubs/rutas ya creados).
- **Módulo Inventario — Fase 2**: CRUD de `Product` con tipos dish/supply/combo, precios (venta/costo/POS), stock, categorías (producto y compra), visible-en-POS, y sincronización de ingredientes (`ProductIngredient`) para platos vía `productos-gestionar` (suite completa: **133 tests**). En Fase 3 se completa Compras + Kardex.
- **Módulo Inventario — Fase 3**: `PurchaseController` (compras con detalle) — al registrar genera automáticamente `KardexMovement` de tipo `purchase` (`quantity_in`) e incrementa el `stock`, todo en una transacción con `compras-gestionar`; vista `show` de compra y **Kardex** de solo lectura con filtro por producto (`kardex-ver`) (suite completa: **145 tests**). Módulo Inventario **completo**.
- **Módulo POS — Avance 1**: salón interactivo (canvas de mesas movibles por arrastre con JS vanilla), apertura/reuso de ventas por mesa, agregado de productos al pedido, envío a cocina, cobro con pagos divididos y pantalla de preparación en cocina. Al pagar, decrementa el stock de insumos (`type=supply`) y genera `KardexMovement` de salida (`movement_type=sale`), liberando la mesa. Suite completa: **164 tests**.
- **Módulo POS — Avance 2**: **Delivery** y **Venta rápida** completos — crean ventas `sale_type = 'delivery'`/`quick_sale` sin mesa con `is_takeaway = true`, guardan proveedor/persona de delivery, y la cocina muestra etiquetas `DELIVERY`/`PARA LLEVAR`. Permisos `pos-delivery`/`pos-venta-rapida` ahora sí se aplican en rutas. **Módulo POS completo** (ventas, cobro, cocina, delivery, venta rápida). Suite completa: **172 tests**.
- **Módulo POS — Factura electrónica**: al cobrar una **factura** se valida un `SunatConfig` vigente (activo, en rango, con cupo) y se exige cliente **empresa**; se asigna **serie** (`nomenclature`+`001`) y **número** (correlativo 8 dígitos) incrementando `used_receipts`, y se genera el **XML UBL 2.1** con **greenter/greenter** v4 (`pos.sale.receipt.xml`) más la vista **imprimible/WhatsApp** (`pos.sale.receipt`). Spatie se descartó (no hay paquete SUNAT). Suite completa: **177 tests**.
- **Módulo Reportes + POS con caja abierta**: **Reporte de Ventas** (`reportes.ventas`, `reportes-ver`) consolida ventas **pagadas** de salón/delivery/venta rápida; **Reporte de Cajas** (`reportes.cajas`, `reportes-cajas-ver`) muestra el historial de aperturas/cierres con montos, responsables y fechas. El **POS ahora exige una caja abierta** (middleware `ensure.pos.session`): las ventas se registran en la sesión activa (`cash_register_session_id`) de salón/delivery/venta rápida hasta su cierre, y al abrir caja se **carry-forwardea** el monto de cierre anterior (editable). Suite completa: **191 tests**.
- **Módulo Usuarios + Roles + Fotos en POS**: CRUD de **usuarios** para dar acceso al sistema (login por email/username), con **asignación de roles** por usuario (mínimo un rol) y **gestión de roles y permisos** (`usuarios-ver`, `usuarios-gestionar`, `roles-gestionar`) con permisos agrupados por módulo; el rol `administrador` no se elimina y el usuario no puede borrarse a sí mismo. En **POS** los platos ahora se muestran **con foto** (`image_url`) y placeholder cuando no hay imagen. Suite completa: **210 tests**.
- **POS Boleta/Factura + Vuelto**: la **boleta** se emite siempre sin exigir RUC (ticket **80 mm**); la **factura** exige una **empresa (RUC) registrada en Clientes** seleccionada al pagar. El pago puede **superar el total**: el vuelto se calcula (`sum_pagos - total`) y se guarda en `sales.change`, mostrándose en pantalla y en el comprobante; el comprobante es un ticket térmico que muestra los pagos por medio y el vuelto. Suite completa: **212 tests**.
- **Estilo panel (dashboard + Salón)**: convención visual grabada en `.ai/rules/views.md` y `docs/DISENO-UI.md` — **texto siempre en oscuro fijo sin `dark:text-*`**, paneles `bg-white`, **bordes casi rectos** (`rounded-lg` tarjetas / `rounded-md` contenedores / `rounded` iconos) y fechas en español con `translatedFormat`. Aplicada al **dashboard** y al **módulo Salón** (CRUD Salones/Mesas + `pos.hall` + partials `status-badge`/`row-actions`), con acento indigo reemplazado por **brand**. Suite completa: **219 tests**.
- **Estilo panel aplicado al POS**: todas las pantallas del Punto de Venta (pedido, cobro, cocina, ticket 80 mm y aviso de caja no abierta) usan ahora **texto oscuro fijo sin `dark:`**, bordes casi rectos y acento **brand** (sustituyendo el indigo obsoleto). Se **conservaron** los acentos funcionales por acción del POS: teal (Delivery), fuchsia (Venta rápida/para llevar), orange (Cocina) y green (Cobrar/Completar). Suite completa: **219 tests**.
- **Empresa única + icono para comprobantes**: En Configuración → Sistema → Empresas se eliminó el alta/borrado (el sistema pertenece a **una sola empresa**); `index` muestra la **tarjeta de detalle** de la empresa con botón **Editar** (permiso `configuracion-editar`). Se agregó un campo de **subida de icono/logo** (`logo` como archivo `image|mimes:jpeg,png,webp|max:2048`, guardado en el disco `public` y **reemplazando el archivo anterior**) que se muestra en la vista previa de edición y en el **ticket 80 mm** de comprobantes/facturas. Se creó `storage:link`. Suite completa: **219 tests**.
- **Login mejorado**: el panel anaranjado usa la **imagen `public/assets/img/restaurant_fondo.jpg`** con **capa oscura** encima para legibilidad del texto blanco; muestra el **logo del restaurante** (el subido en Empresas, con respaldo 🍽️ si no hay) tomado de la primera `Company` vía `LoginController@create`; y los **bordes del login son más rectos** (`rounded-md`, sin `rounded-xl/2xl`) y sin variantes `dark:`. Suite completa: **220 tests**.

## Siguiente paso
- **Reportes por rangos de fecha y exportación (PDF/Excel)**, comparativa cierre de caja vs `sales_sum_total`, y panel de dashboard con métricas.
- En **producción** (facturación): elegir la Compañía emisora con su `ubigeo`, firmar el XML (certificado digital), credenciales **SOL** y endpoint SUNAT para envío real y respuesta CDR; la boleta electrónica (`identification`) seguiría el mismo patrón (tipo `03`).
