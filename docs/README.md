# Sistema de Restaurante — Guía de Construcción del Proyecto (Laravel)

Este README explica cómo levantar el proyecto desde cero para que funcione tal como se describe en el documento funcional (`Modelado_de_la_BD.docx`): un sistema que cubre todo el ciclo de un restaurante, desde el **Punto de Venta (POS)** hasta **Inventario, Cajas, Clientes y Configuración**.

---

## 1. Módulos del sistema

El documento define **5 módulos principales**, cada uno con submódulos:

| Módulo | Submódulos | Roles con acceso |
|---|---|---|
| **POS** | Ventas, Preparación (cocina), Delivery, Venta Rápida | Administrador, Mozo, Chef, Contabilidad (según submódulo) |
| **Clientes** | Clientes, Empresas | Administrador, Mozo, Contabilidad |
| **Cajas** | Apertura/cierre de caja | Administrador, Contabilidad |
| **Inventario** | Compras, Productos (Stock, Platos/Insumos/Categorías), Kardex | Administrador, Contabilidad |
| **Configuración** | Sistema (inicial, empresa, roles y permisos, doc./pagos), General (SUNAT y comisiones, optimización), Restaurante (cajas/turnos, mesas/salones, delivery) | Administrador (y Contabilidad en varias secciones) |

> El documento menciona "6 grandes módulos" pero solo detalla 5; si más adelante aparece un módulo de Reportes/Contabilidad independiente, se integra igual que los demás siguiendo esta misma guía.

---

## 2. Roles del sistema

Los roles (**Administrador, Chef, Mozo, Contabilidad**) controlan qué módulos ve cada usuario. El documento indica explícitamente que esto se maneja con **Spatie Laravel Permission**, no con tablas propias de roles.

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

En `app/Models/User.php` agrega el trait:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    // ...
}
```

Crea un seeder de roles y permisos (`RolePermissionSeeder`) con al menos:

```php
$roles = ['administrador', 'chef', 'mozo', 'contabilidad'];
foreach ($roles as $role) {
    Role::firstOrCreate(['name' => $role]);
}
```

Y protege rutas/menús con middleware `role:` o `can:` según el módulo (ver tabla del punto 1).

---

## 3. Requisitos previos

- PHP >= 8.2
- Composer
- MySQL o MariaDB
- Node.js + npm (para compilar assets del front-end)
- Extensión `pdo_mysql` habilitada

---

## 4. Instalación paso a paso

```bash
# 1. Crear el proyecto
composer create-project laravel/laravel restaurante-sistema
cd restaurante-sistema

# 2. Configurar entorno
cp .env.example .env
php artisan key:generate
```

Edita `.env` con tus credenciales de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurante_sistema
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 3. Instalar dependencias adicionales necesarias para el flujo descrito
composer require spatie/laravel-permission     # Roles y permisos
composer require barryvdh/laravel-dompdf       # Boletas / Facturas en PDF
composer require intervention/image            # Fotos de platos e imagen de la empresa
```

> **Notificación por WhatsApp** (envío de comprobante): el documento la menciona como opción en la sección de Cobro. No hay un paquete oficial de Laravel para esto; se integra vía la API de WhatsApp Business o un proveedor externo (Twilio, Meta Cloud API, etc.) cuando llegues a esa parte — no es necesaria para que el resto del sistema funcione.

> **Actualización en tiempo real de cocina** (submódulo Preparación): para que las comandas aparezcan sin recargar la página, usa Laravel Echo + un driver de broadcasting (Pusher, Reverb, o Soketi). Instálalo cuando implementes ese submódulo; el resto del sistema no depende de esto.

---

## 5. Base de datos: orden de creación

Sigue exactamente el orden de migraciones y modelos que ya se generó en `migraciones_y_modelos_laravel.md` (28 tablas, 8 grupos de dependencia). Resumen rápido:

```bash
php artisan make:model Ubigeo -m
php artisan make:model Timezone -m
php artisan make:model Currency -m
php artisan make:model DocumentType -m
php artisan make:model PaymentMethod -m
php artisan make:model Company -m
php artisan make:model SunatConfig -m
# users: editar migración/modelo por defecto de Laravel
php artisan make:model Hall -m
php artisan make:model Table -m
php artisan make:model Shift -m
php artisan make:model CashRegister -m
php artisan make:model DeliveryProvider -m
php artisan make:model CashRegisterSession -m
php artisan make:model Customer -m
php artisan make:model CompanyClient -m
php artisan make:model PurchaseCategory -m
php artisan make:model ProductCategory -m
php artisan make:model Supplier -m
php artisan make:model Product -m
php artisan make:model ProductIngredient -m
php artisan make:model Purchase -m
php artisan make:model PurchaseDetail -m
php artisan make:model Sale -m
php artisan make:model SaleTable -m
php artisan make:model SaleDetail -m
php artisan make:model SalePayment -m
php artisan make:model KardexMovement -m

php artisan migrate
```

Copia el código de cada migración y modelo desde `migraciones_y_modelos_laravel.md` a los archivos generados, en el mismo orden.

---

## 6. Seeders indispensables antes de usar el sistema

El documento aclara varias **reglas de negocio** que exigen datos base antes de que POS funcione:

1. **`RolePermissionSeeder`** → roles Administrador, Chef, Mozo, Contabilidad.
2. **`DocumentTypeSeeder`** → al menos BOLETA y FACTURA (tipo `invoice`), y un tipo `identification` (DNI/RUC) con su nomenclatura y límite de caracteres.
3. **`PaymentMethodSeeder`** → Efectivo, Tarjeta, Yape, Plin.
4. **`CurrencySeeder`** → moneda por defecto (ej. Sol, `S/.`, `is_default = true`).
5. **`TimezoneSeeder`** → zona horaria del restaurante (se autodetecta según ubicación, pero debe existir un registro editable).
6. **`UbigeoSeeder`** → catálogo de departamento/provincia/distrito (para la dirección fiscal de la empresa).
7. **`CompanySeeder`** → datos generales de la empresa y datos SOL (usuario, clave, certificado digital) — sin esto no se pueden emitir comprobantes.
8. **`SunatConfigSeeder`** → configuración de facturación vigente (`start_date`, `end_date`, `max_receipts`). **Regla crítica del documento:** si se agotan los comprobantes o vence la fecha, el sistema **no debe permitir emitir ventas** — esta validación va en el controlador de `Sale`, no solo en el seeder.
9. **`ProductCategorySeeder`** → categorías de platos que se muestran en POS (Bebidas, Postres, Comida Caliente, Comida Criolla, etc.).
10. **`PurchaseCategorySeeder`** → Materia Prima e Insumo (las dos categorías de compra que pide el documento).
11. **`HallSeeder` + `TableSeeder`** → al menos un salón con mesas, o POS no tendrá mesas que mostrar.
12. **`ShiftSeeder`** → turnos (mañana/tarde/noche).
13. **`CashRegisterSeeder`** → al menos una caja (ej. "Caja Principal"). **Regla crítica del documento:** POS no funciona sin al menos una caja creada.

> Sin los puntos 7, 8, 11 y 13, el módulo POS no debe operar — replica esas validaciones en el backend, no solo confíes en que el usuario cargue los datos en orden.

---

## 7. Cómo cada módulo del documento se traduce a las tablas/modelos ya creados

### 7.1 Configuración → primero, siempre
- **Configuración Inicial**: `Timezone`, `Currency`, `DocumentType`.
- **Datos de la Empresa**: `Company` (+ `Ubigeo` para la dirección).
- **SUNAT y Comisiones**: `SunatConfig` (vigencia y tope de comprobantes) + campo `card_surcharge_percentage` (recargo por tarjeta).
- **Roles y Permisos**: Spatie (no hay tablas propias, según el documento).
- **Restaurante**: `Hall`, `Table`, `Shift`, `CashRegister`, `DeliveryProvider` — se crean aquí, **no** en el módulo de Cajas ni en POS.

### 7.2 Clientes
- `Customer` (persona natural) y `CompanyClient` (empresa) — ambos con CRUD simple (ver, listar, crear, eliminar) y `status` activo por defecto, tal como pide el documento.
- Ambos se conectan a `Sale` mediante la relación polimórfica `clientable` (si no se especifica cliente, la venta queda como "público general", es decir `clientable_id` nulo).

### 7.3 Cajas
- `CashRegisterSession`: aquí **solo se abre y se cierra** una caja ya existente (creada en Configuración → Restaurante). Al abrir se pide `shift_id`, `user_opening_id` (automático, del usuario logueado) y `opening_amount`. Al cerrar se pide `user_closing_id` y `closing_amount`.
- **Regla de negocio del documento**: si una caja cerró con S/. 1000, la siguiente apertura de esa caja debe reutilizar ese monto como sugerencia de `opening_amount` — impleméntalo en el controlador (tomando el `closing_amount` de la última sesión cerrada de esa caja).

### 7.4 Inventario
- **Proveedores** (`Supplier`) → necesarios antes de poder comprar.
- **Compras** (`Purchase` + `PurchaseDetail`) → primero se crean las `PurchaseCategory` (Materia Prima / Insumo), luego la compra con su proveedor, tipo de comprobante (de `DocumentType`) y el detalle de productos comprados.
- **Productos** (`Product`):
  - *Stock*: son los productos comprados (materia prima/insumos), de solo lectura + búsqueda.
  - *Platos e Insumos*: requieren `ProductCategory` previamente creada. Un plato (`type = 'dish'`) usa `ProductIngredient` para vincular sus insumos (relación producto-producto). Un insumo (`type = 'supply'`) puede autocompletarse desde compras y se le agrega `sale_price` (precio de venta) además del `cost_price` que ya trae.
  - El **stock disminuye automáticamente** cuando el insumo se vende en POS — esto se implementa restando `quantity` en el `SaleDetail` al `stock` del `Product`, dentro de la misma transacción que crea la venta.
- **Kardex** (`KardexMovement`): módulo de solo lectura/reportes. Se genera automáticamente:
  - Al confirmar una `Purchase` → un `KardexMovement` con `movement_type = 'purchase'`, `quantity_in`, `related_document` apuntando a esa compra.
  - Al confirmar un `SaleDetail` de un insumo → un `KardexMovement` con `movement_type = 'sale'`, `quantity_out`, `related_document` apuntando a esa venta.
  - No se crea manualmente desde una vista; se genera vía Observers o dentro del `Service`/`Action` que procesa la compra/venta.

### 7.5 POS (el módulo más grande)

**Ventas (mesero en salón):**
1. Vista de `Hall` → `Table` con su `status` (`available`, `occupied`, `reserved`).
2. Al hacer clic en una mesa (o varias, vía `SaleTable`, aunque la venta es una sola `Sale`) se abre el formulario con `guests` y el mozo (`user_id`, automático de la sesión).
3. Se crea la `Sale` en estado `pending`, con `sale_type = 'pos'`.
4. Sección de productos (`Product` filtrados por `ProductCategory`, con buscador) → cada click agrega un `SaleDetail`.
5. El mozo puede modificar `quantity`, eliminar el `SaleDetail`, o agregar `notes` (ej. "1 ceviche picante y el otro normal").
6. Tres acciones: **Enviar a preparación** (marca los `SaleDetail.kitchen_status = 'pending'` y aparecen en el submódulo de Cocina), **Cobrar** o **Dividir cuenta** (ambas llevan a la sección de Cobro).

**Cobro:**
- Resumen de `SaleDetail` (detalle) + selección opcional de `Customer`/`CompanyClient` (si no se elige, queda "público general").
- Selección de `DocumentType` (Boleta/Factura) — **validar que `SunatConfig` esté vigente antes de permitir emitir**.
- Uno o varios `SalePayment` (permite dividir el pago entre varios `PaymentMethod`, cada uno con su `amount`) hasta cubrir el `total`.
- Al confirmar: `Sale.status = 'paid'`, se generan los `KardexMovement` correspondientes, y se ofrece imprimir el comprobante (PDF con `barryvdh/laravel-dompdf`) o enviarlo por WhatsApp.

**Preparación (Chef):**
- Vista tipo tarjetas de `SaleDetail` con `kitchen_status = 'pending'`, agrupadas por `Sale` (mesa, hora, platos, notas).
- Botón "Marcar en preparación" → `kitchen_status = 'preparing'`, guarda `prep_started_at` (para el contador de tiempo en el front-end).
- Botón "Completado" → `kitchen_status = 'completed'`, guarda `prep_completed_at`. La tarjeta desaparece de la vista principal pero sigue disponible en un listado tipo tabla (filtro `kitchen_status`, sin excluir completados).

**Delivery:**
- Mismo flujo de Ventas + Cobro, pero `sale_type = 'delivery'`, sin `table_id`, con `delivery_provider_id` (proveedor o personal propio) y `delivery_person_name`. Al enviarse a preparación, se marca claramente como "DELIVERY — Para llevar" (usa el propio `sale_type` para renderizar esa etiqueta en la vista de Preparación).

**Venta Rápida:**
- Igual que Ventas + Cobro, pero `sale_type = 'quick_sale'`, sin mesa. Al enviarse a preparación se marca como "PARA LLEVAR" usando `is_takeaway = true`.

---

## 8. Orden recomendado de implementación (de la base hacia la interfaz)

1. Migraciones y modelos (sección 5).
2. Spatie roles/permisos + middleware de acceso por módulo (sección 2).
3. Seeders de catálogos base (sección 6) — sin esto no puedes probar nada.
4. CRUD de Configuración → Restaurante (`Hall`, `Table`, `Shift`, `CashRegister`, `DeliveryProvider`).
5. CRUD de Configuración → Sistema (`DocumentType`, `PaymentMethod`, `Company`, `SunatConfig`).
6. Módulo de Cajas (apertura/cierre de `CashRegisterSession`), con la validación de "no operar POS sin caja abierta".
7. Módulo de Clientes (`Customer`, `CompanyClient`).
8. Módulo de Inventario: Proveedores → Categorías de compra/producto → Compras → Productos (Stock, Platos/Insumos) → Kardex (automático).
9. Módulo POS: Ventas → Preparación → Cobro → Delivery → Venta Rápida (en ese orden, porque Delivery y Venta Rápida reutilizan la lógica de Ventas/Cobro).
10. Reportes / exportación de comprobantes en PDF y envío por WhatsApp (opcional, al final).

---

## 9. Reglas de negocio a no olvidar (extraídas del documento)

- No se puede operar POS sin al menos **una caja creada y abierta**.
- No se pueden emitir comprobantes si `SunatConfig` está vencida o si `used_receipts >= max_receipts`.
- El monto de apertura de una caja debe sugerir el `closing_amount` de su sesión anterior.
- Una venta puede *ocupar* varias mesas (`SaleTable`) pero pertenece a **una sola** `Sale`.
- Los insumos (no los platos) descuentan `stock` automáticamente al venderse, y generan un `KardexMovement`.
- Las compras siempre generan `KardexMovement` de tipo entrada (`quantity_in`).
- Un cliente en la venta es opcional: si no se especifica, se factura a "público general" (`clientable_id` nulo).
- El pago de una venta puede dividirse entre varios métodos de pago simultáneamente (`SalePayment` es una tabla, no un campo único en `Sale`).
 
---

## 10. Comandos finales

```bash
php artisan migrate --seed     # Migra todo y corre los seeders base
npm install && npm run build   # Compila el front-end
php artisan serve              # Levanta el servidor local
```



