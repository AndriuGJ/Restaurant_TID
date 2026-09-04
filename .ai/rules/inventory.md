---
paths:
  - 'app/Http/Controllers/Inventory/**'
---

# Inventory

## Inventario: catálogos con permiso inventario-ver
Los CRUDs de Inventario (Supplier, PurchaseCategory, ProductCategory) usan middleware/permiso `inventario-ver` para toda acción (ver/crear/editar/borrar) vía rutas explícitas en el grupo `inventory.*`. El partial `row-actions` recibe `permission => 'inventario-ver'` (default `configuracion-editar`). Rutas por fases: Fase 2 agrega `inventory.products.*`, Fase 3 `inventory.purchases.*` y `inventory.kardex.*`; estas tres ya tienen controladores/stubs y rutas `index`. Destroy de catálogos bloqueado si tiene registros relacionados.

## Productos: productos-gestionar y sincronizar ingredientes
ProductController usa `productos-gestionar` para create/store/edit/update/destroy y `inventario-ver` para index. Los productos `type=dish` sincronizan sus insumos en `product_ingredients` vía `syncIngredients()` (borra y reinserta) dentro de una transacción. El `_form` de producto usa JS vanilla (data-ingredient-row/add/remove) para la sección de ingredientes — NO usar Alpine (no está instalado). En el partial row-actions pasa `permission => 'productos-gestionar'`. El factory ProductFactory tiene estados `dish()`/`supply()`/`combo()`.

## Compras: transacción + kardex automático
PurchaseController usa `compras-gestionar` para create/store y `inventario-ver` para index/show (`inventory.purchases.*`). Al registrar una compra, en una transacción: crea Purchase (status completed, user_id = auth user), PurchaseDetail por línea, incrementa `product->stock` y genera un `KardexMovement` `movement_type=purchase`, `quantity_in`, `balance=stock` tras incrementar, con `related_document_type=Purchase::class` y `related_document_id`. StorePurchaseRequest restringe `document_type_id` a `type=invoice` (Rule::exists->where). `KardexController@index` usa permiso `kardex-ver` y filtra por query `?product=`. En el ShowRequest el checkout del formulario puede REPUARDARSE; no hay update/destroy de compras.
