---
paths:
  - 'app/Http/Controllers/Restaurant/**'
---

# Restaurant

## Detección de IP de LAN: no usar gethostbyname del hostname
gethostbyname(gethostname()) en Ubuntu devuelve 127.0.1.1 (loopback que responde en todo 127/8), por eso el escaneo inicial listó "impresoras" falsas. Para detectar la IP de LAN del servidor usar exec('hostname -I'), priorizar rangos privados y bloquear rangos 127.x en el escaneo (PrinterController::lanIP/lanRange).
