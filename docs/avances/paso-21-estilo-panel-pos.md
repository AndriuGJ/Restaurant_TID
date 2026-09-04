# Paso 21 — Estilo panel aplicado al módulo POS (Punto de Venta)

Fecha: 2026-09-03

## Objetivo
Extender la convención visual (texto oscuro fijo sin `dark:`, bordes casi rectos, acento **brand** en vez de indigo) a todas las pantallas del **Punto de Venta** (POS): pedido, cobro, cocina, comprobante 80 mm y el aviso de "caja no abierta".

## Qué se hizo
Para cada vista se eliminaron las variantes `dark:*`, se fijaron los textos en grises oscuros (`text-gray-900/700/600/500`), se ajustaron los contenedores a `rounded-md` y se sustituyó el acento **indigo** por el **brand** (naranja) solo donde corresponde a la acción general:

- **`pos/sale.blade.php`** (pedido/venta en salón, delivery y venta rápida):
  - Buscador de productos, inputs de cantidad/nota y actualización de cantidad → `focus:border-brand-500`.
  - Botones de categoría y "Agregar al pedido" → brand; precio del producto `text-indigo-600` → `text-brand-600`.
  - Tarjetas de catálogo y detalle `rounded-lg` → `rounded-md`; se quitó `dark:` de etiquetas DELIVERY/PARA LLEVAR y del detalle.
  - JS de categorías: clase activa pasó de `bg-indigo-600` → `bg-brand-500` (sin `dark:`).
- **`pos/checkout.blade.php`** (cobro):
  - Selecciones de cliente/comprobante, comensales y pagos → `focus:border-brand-500`; enlace "+ Agregar pago" → brand.
  - Tarjetas de resumen y formulario `rounded-md`; sin `dark:`.
  - JS: template del pago (blade + JS) en brand sin `dark:`; color del resumen de pagos sin `dark:`.
- **`pos/kitchen.blade.php`** (cocina):
  - Tarjeta de pedido `rounded-md`; botón "Iniciar" `bg-indigo-600` → `bg-brand-500`; sin `dark:` en etiquetas de estado/DELIVERY/PARA LLEVAR.
- **`pos/receipt.blade.php`** (comprobante/ticket 80 mm):
  - Se conservó el ticket limpio en `text-gray-900` (ya fijo); se quitaron los `dark:` del contenedor y de los divisores punteados.
  - Botón "Imprimir ticket" `bg-indigo-600` → `bg-brand-500`; botón "Volver al punto de venta" sin `dark:`.
- **`pos/requires-session.blade.php`** (caja no abierta):
  - Tarjeta `rounded-md`; botón "Abrir caja" → brand; sin `dark:`.

## Mejora del canvas del salón (mesas arrastrables)
Rediseño de `pos/hall.blade.php` y `pos/_table-node.blade.php`:
- **Barra de acciones** en una tarjeta `rounded-md` (`bg-white`) con selector de salón, divisor y botones de **Delivery**, **Venta rápida** y **Cocina** con **iconos SVG inline** (teal/fuchsia/brand/orange).
- **Leyenda** en tarjeta propia con chips (Disponible/Ocupada/Reservada) y una pista "Arrastra para mover · clic para abrir".
- **Canvas minimalista "solo líneas"**: superficie clara `bg-gray-50` con patrón de **puntitos** (radial-gradient 24px) y borde gris claro — sin rellenos de piso ni sombras pesadas.
- **Mesas en líneas** (`_table-node`): cada mesa es un **contorno** (`border-2 border-dashed`) del color de estado (emerald/sky/amber) con el nombre en el interior (`bg-white/60`), que se vuelve `border-solid` en hover. Se respeta la forma (round/square/rectangular). **Sin** etiquetas pill ni gradientes.
- **Estado vacío**: si el salón no tiene mesas se muestra un mensaje centrado con icono y enlace "Agregar mesas".

## Nota sobre colores funcionales del POS
Se **conservaron** los acentos de acción específicos que no corresponden a la marca general:
- **teal** (Delivery), **fuchsia** (Para llevar/Venta rápida), **orange** (Cocina/Enviar a cocina) y **green** (Cobrar/Confirmar pago/Completar). Solo se reemplazó el **indigo** (que era el acento obsoleto y genérico) por el brand.

## Verificación
- `npm run build` OK; `php artisan view:clear` aplicado.
- Grep: sin `dark:` ni `indigo` en `resources/views/pos/*.blade.php`.
- Suite completa: **219/219** tests pasando.

## Próximo
Aplicar el mismo criterio al resto de pantallas del panel (inventario, clientes, cajas/sesiones, reportes, configuración, usuarios/roles) para uniformar todo el sistema.