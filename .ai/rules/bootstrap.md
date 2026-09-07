---
paths:
  - bootstrap/app.php
---

# Bootstrap

## No-store global: PreventRequestCaching en grupo web
PreventRequestCaching se añade globalmente a las rutas web (append en bootstrap/app.php) y envía Cache-Control no-store/no-cache en todas las respuestas del panel para evitar que el navegador sirva HTML/JS viejo (páginas Blade/POS). No aplica a respuestas en consola/phpunit (estas no pasan el group 'web' de middleware en el kernel HTTP; los tests usan el miedo habitual de middleware por defecto). Para cambios de UI recuerda hard refresh igualmente.
