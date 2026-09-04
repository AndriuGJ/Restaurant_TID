# Paso 23 — Mejoras al login: fondo con overlay y logo del restaurante

Fecha: 2026-09-03

## Objetivo
Mejorar la pantalla de **login**:
1. En la mitad anaranjada (panel de marca) mostrar una **imagen de fondo con una capa oscura** encima para que las **letras blancas** se lean bien.
2. Hacer los **bordes más rectos** (menos redondeo).
3. Mostrar ahí el **logo del restaurante**.

## Qué se hizo

### Imagen de fondo (panel de marca)
- El panel izquierdo (`layouts/guest.blade.php`, `lg:w-[44%]`) ahora muestra la foto `public/assets/img/restaurant_fondo.jpg` a todo el panel (`url('/assets/img/restaurant_fondo.jpg')`).
- Encima se aplica una **capa oscura** (`bg-gray-950/70` + degradado `bg-gradient-to-t`) para que el texto blanco sea legible sobre cualquier foto.

### Logo del restaurante
- El `LoginController@create` ahora consulta la **primera empresa** (`Company::query()->first()`) y se la pasa a la vista.
- En el encabezado del panel se muestra el **logo subido de la empresa** (`asset('storage/'.$company->logo)`) junto al nombre comercial; si no hay logo, se muestra el ícono 🍽️ como respaldo.
- Se usan accesos null-safe (`$company?->...`) para que la vista no falle si aún no hay empresa registrada.

### Bordes más rectos
- En `auth/login.blade.php` se bajó el redondeo de los contenedores/inputs/botón:
  - Tarjeta del formulario: `rounded-2xl` → `rounded-md`.
  - Inputs, aviso de error y botón "Iniciar sesión": `rounded-xl` → `rounded-md`.
  - Íconos de encabezado: `rounded-2xl` → `rounded-md`.
- Se **eliminaron** todas las variantes `dark:` del login (convención de texto oscuro fijo / panel blanco) y no queda ningún `rounded-2xl`/`rounded-xl` en el login ni en el layout guest.

## Verificación
- `npm run build` OK; `php artisan view:clear` aplicado.
- Grep: sin `dark:`, `rounded-2xl` ni `rounded-xl` en `auth/login.blade.php` y `layouts/guest.blade.php`.
- Tests de Auth: **7/7 OK**. Suite completa: **220/220** tests pasando.

## Nota
- La imagen de fondo se referencia de forma local (`/assets/img/restaurant_fondo.jpg`), no depende de la máquina.
- El logo mostrado es el que se sube en **Configuración → Sistema → Empresas** (paso 22).

## Próximo
Aplicar la misma convención visual al resto de pantallas del panel (inventario, clientes, cajas, reportes, usuarios/roles).