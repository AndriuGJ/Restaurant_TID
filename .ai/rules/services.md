---
paths:
  - 'app/Services/**'
  - app/Services/PrinterService.php
---

# Services

## Impresión ESC/POS vía socket (sin dependencias)
PrinterService (App\Services) imprime a impresoras térmicas de la red usando fsockopen al puerto 9100 (RAW ESC/POS) de la impresora marcada is_default/is_active. No usar librerías externas. width=32 chars; comandos ESC @, ESC E, GS ! 0x11, ESC d, GS V B. Al cambiar is_default hay que limpiar las demás (solo una caja principal).

## Impresora local (USB/Cable) vía CUPS `lp -o raw`
PrinterService soporta 2 modos vía connection_type del modelo Printer: 'network' (socket fsockopen ip:port 9100) y 'local' (CUPS: pipe de bytes ESC/POS a lp -d QUEUE -o raw, captura código/stderr con proc_open). Un mismo comprobante se imprime igual en ambos; en local NO se usa IP.

## Local printing: raw ESC/POS vs driver PDF
A local (CUPS) printer has two output modes via `send_raw`. send_raw=true sends the ESC/POS bytes with `lp -d QUEUE -o raw` (thermal only). send_raw=false renders the receipt with TcpdfService (view `pos/print`) and calls `lp -d QUEUE FILE.pdf` WITHOUT `-o raw` so CUPS drivers (inkjet/laser) interpret it. `PrinterController::queues()` lists real CUPS queues via `lpstat -p`; never hardcode a queue name — the app must not assume a printer is installed.

## TcpdfService: TCPDF 7 + fuentes + K_PATH_FONTS
TcpdfService (App\Services) genera PDFs con TCPDF 7 (tecnickcom/tcpdf) renderizando vistas Blade via writeHTML: render(view, data, paper) devuelve bytes; logoDataUri() incrusta el logo de Company en base64. TRAMPAS: (1) TCPDF 7 NO trae fuentes — hay que ejecutar `make fonts` en vendor/tecnickcom/tc-lib-pdf-font una vez (genera target/fonts/, 71 fuentes); (2) el autoconfig de K_PATH_FONTS resuelve una ruta anidada inexistente bajo Composer plano — TcpdfService define K_PATH_FONTS=base_path('vendor/tecnickcom/tc-lib-pdf-font/target/fonts/') antes de instanciar; (3) usar la clase legacy `new \TCPDF(...)` (orient, mm, formato, unicode, UTF-8), NO Com\Tecnick\Pdf\Tcpdf (otra firma). Tipografia dejavusans para acentos. Reemplaza a DomPDF (barryvdh ya no se usa).
