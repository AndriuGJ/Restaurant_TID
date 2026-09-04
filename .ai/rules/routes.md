---
paths:
  - routes/web.php
---

# Routes

## CRUD con permisos finos y rutas explicitas
Define rutas de CRUD explicitas (no Route::resource) para aplicar permisos distintos por accion: configuracion-ver en index, configuracion-editar en create/store/edit/update/destroy. Agrupar bajo prefix + name y dentro de middleware auth. Paginacion Tailwind via Paginator::useTailwind en AppServiceProvider.

## Prefijo Configuration usa configuración y nombre configuration.*
Los CRUDs de Configuración → Sistema usan Route::prefix('configuracion')->name('configuration.') con permisos finos configuracion-ver (index) y configuracion-editar (create/store/edit/update/destroy).

## Rutas de Cajas (apertura/cierre) con permisos propios
El módulo Cajas usa Route::prefix('cajas')->name('cash-registers.sessions.') con permisos finos: cajas-ver (index), cajas-abrir (create/store), cajas-cerrar (edit/update). Las sesiones solo se abren y cierran, no hay destroy.

## Rutas de Clientes: prefijo clientes con subgrupo empresas
Clientes usan Route::prefix('clientes')->name('customers.') con permisos clientes-ver (index) y clientes-gestionar (mutation). CompanyClient va bajo clientes/empresas → name customers.companies.*. Registrar clientes/empresas ANTES de clientes/{customer} para evitar conflicto de parámetros.
