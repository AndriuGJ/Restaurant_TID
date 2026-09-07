<?php

namespace App\Services;

use App\Models\Configuration\Company;
use App\Models\Restaurant\Printer;
use App\Models\Sales\Sale;

class PrinterService
{
    private int $width = 32;

    public function defaultPrinter(): ?Printer
    {
        return Printer::query()
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Busca impresoras con el puerto 9100 abierto en un rango de IPs.
     *
     * @return array<int, array{ip: string, host: ?string}>
     */
    public function discover(string $from, string $to, int $port = 9100): array
    {
        $start = ip2long($from);
        $end = ip2long($to);

        if ($start === false || $end === false) {
            return [];
        }

        $sockets = [];
        $addresses = [];
        $timeout = 0.5;

        for ($long = $start; $long <= $end; $long++) {
            $address = long2ip($long);
            $sock = @stream_socket_client(
                "tcp://{$address}:{$port}",
                $errno,
                $errstr,
                $timeout,
                STREAM_CLIENT_CONNECT | STREAM_CLIENT_ASYNC_CONNECT
            );

            if (is_resource($sock)) {
                $sockets[(int) $sock] = $sock;
                $addresses[(int) $sock] = $address;
            }
        }

        $found = [];
        $deadline = microtime(true) + $timeout + 2;

        while ($sockets !== [] && microtime(true) < $deadline) {
            $read = $write = $except = $sockets;
            $selected = @stream_select($read, $write, $except, 0, 200000);

            if ($selected === false) {
                break;
            }
            if ($selected === 0) {
                continue;
            }

            foreach ($write as $sock) {
                $found[] = $addresses[(int) $sock];
                unset($sockets[(int) $sock]);
                fclose($sock);
            }
            foreach ($except as $sock) {
                unset($sockets[(int) $sock]);
                fclose($sock);
            }
        }

        foreach ($sockets as $sock) {
            fclose($sock);
        }

        $printers = array_values(array_unique($found));

        $printers = array_map(function (string $ip) use ($port): array {
            $host = @gethostbyaddr($ip);

            return [
                'ip' => $ip,
                'host' => ($host === false || $host === $ip) ? null : $host,
                'model' => $this->identify($ip, $port),
            ];
        }, $printers);

        usort($printers, fn (array $a, array $b): int => ip2long($a['ip']) <=> ip2long($b['ip']));

        return $printers;
    }

    /**
     * Interroga el puerto 9100 con comandos ESC/POS (GS I) para identificar el modelo.
     */
    private function identify(string $address, int $port): ?string
    {
        $fp = @fsockopen($address, $port, $errno, $errstr, 0.4);

        if ($fp === false) {
            return null;
        }

        stream_set_timeout($fp, 0);

        fwrite($fp, "\x1D\x49\x01\x1D\x49\x30\x1D\x49\x31\x1B\x40");

        $data = '';
        $deadline = microtime(true) + 0.7;

        while (microtime(true) < $deadline) {
            $read = [$fp];
            $write = null;
            $except = null;
            $selected = @stream_select($read, $write, $except, 0, 150000);

            if ($selected === false) {
                break;
            }
            if ($selected === 0) {
                continue;
            }

            $chunk = @fread($fp, 8192);
            if ($chunk === false) {
                break;
            }
            if ($chunk === '') {
                continue;
            }
            $data .= $chunk;
        }

        fclose($fp);

        $text = trim((string) preg_replace('/[^\x20-\x7E]/', '', $data));

        return $text !== '' ? $text : null;
    }

    /**
     * @return array{0: bool, 1: string}
     */
    public function test(Printer $printer): array
    {
        if ($printer->connection_type === 'local' && ! $printer->send_raw) {
            try {
                $html = '<h2>PRUEBA DE IMPRESORA</h2>'
                    .'<p>'.$printer->name.'</p>'
                    .'<p>Si ves esta página impresa, la impresora funciona correctamente.</p>'
                    .'<p>'.now()->format('d/m/Y H:i').'</p>';

                // Reuse the render pipeline by echoing the HTML into a tiny view.
                $pdf = app(TcpdfService::class)->render('pos.print-test', ['html' => $html]);

                return $this->sendDrivenPdf($printer, $pdf);
            } catch (\Throwable $e) {
                return [false, 'No se pudo generar el PDF de prueba: '.$e->getMessage()];
            }
        }

        $bytes = "\x1B\x40"
            .$this->line('PRUEBA DE IMPRESORA', 'center', bold: true)
            .$this->line($printer->name, 'center')
            .$this->line('Si ves este ticket,', 'center')
            .$this->line('la impresora funciona.', 'center')
            .$this->feed(3)
            .$this->cut();

        return $this->send($printer, $bytes);
    }

    /**
     * @return array{0: bool, 1: string}
     */
    public function printSale(Sale $sale, Company $company): array
    {
        $printer = $this->defaultPrinter();

        if (! $printer) {
            return [false, 'No hay ninguna impresora asignada como caja principal.'];
        }

        $sale->load(['documentType', 'clientable', 'details.product', 'payments.paymentMethod']);

        if ($printer->connection_type === 'local' && ! $printer->send_raw) {
            try {
                $pdf = app(TcpdfService::class)->render('pos.print', ['sale' => $sale, 'company' => $company]);

                return $this->sendDrivenPdf($printer, $pdf);
            } catch (\Throwable $e) {
                return [false, 'No se pudo generar el PDF del comprobante: '.$e->getMessage()];
            }
        }

        $bytes = "\x1B\x40".$this->buildTicket($sale, $company);

        return $this->send($printer, $bytes);
    }

    private function buildTicket(Sale $sale, Company $company): string
    {
        $isFactura = $sale->documentType?->nomenclature !== null
            && mb_strtoupper($sale->documentType->nomenclature) === 'F';
        $documentLabel = $isFactura ? 'FACTURA' : 'BOLETA';
        $payout = $sale->payments->sum('amount');
        $date = optional($sale->updated_at)->format('d/m/Y H:i');

        $out = '';

        $out .= $this->line(mb_strtoupper($company->social_reason), 'center', bold: true);
        if ($company->commercial_name) {
            $out .= $this->line($company->commercial_name, 'center');
        }
        if ($company->ruc) {
            $out .= $this->line("RUC: {$company->ruc}", 'center');
        }
        if ($company->fiscal_address) {
            $out .= $this->line($company->fiscal_address, 'center');
        }

        $out .= $this->divider();

        $out .= $this->line("{$documentLabel} ELECTRÓNICA", 'center', double: true);
        $out .= $this->line("{$sale->series}-{$sale->number}", 'center', bold: true);
        $out .= $this->line("Fecha: {$date}");
        $out .= $this->line("Pedido: #{$sale->id}");

        if ($isFactura && $sale->clientable) {
            $out .= $this->line('Cliente: '.$sale->clientable->social_reason);
            $out .= $this->line('RUC: '.$sale->clientable->ruc);
        }

        $out .= $this->divider();

        foreach ($sale->details as $detail) {
            $out .= $this->row(
                (string) $detail->product?->name,
                'x'.format_quantity($detail->quantity).' '.$this->money($detail->unit_price)
            );
            $out .= $this->row('', "S/ {$this->money($detail->subtotal)}");
        }

        $out .= $this->divider();

        $out .= $this->row('Subtotal', "S/ {$this->money($sale->subtotal)}");
        $out .= $this->row('IGV (18%)', "S/ {$this->money($sale->subtotal * 0.18)}");
        $out .= $this->row('TOTAL', "S/ {$this->money($sale->total)}", bold: true);

        $out .= $this->divider();

        foreach ($sale->payments as $payment) {
            $out .= $this->row(mb_strtoupper((string) $payment->paymentMethod?->name), "S/ {$this->money($payment->amount)}");
        }
        $out .= $this->row('Total pagado', "S/ {$this->money($payout)}");
        if ((float) $sale->change > 0) {
            $out .= $this->row('VUELTO', "S/ {$this->money($sale->change)}", bold: true);
        }

        $out .= $this->feed(4);
        $out .= $this->cut();

        return $out;
    }

    private function line(string $text, string $align = 'left', bool $bold = false, bool $double = false): string
    {
        $text = mb_substr($text, 0, $this->width);
        $padding = max(0, $this->width - mb_strlen($text));

        if ($align === 'center') {
            $gutter = intdiv($padding, 2);
            $text = str_repeat(' ', $gutter).$text;
        } elseif ($align === 'right') {
            $text = str_repeat(' ', $padding).$text;
        } else {
            $text .= str_repeat(' ', $padding);
        }

        $out = '';

        if ($double) {
            $out .= "\x1D\x21\x11";
        } elseif ($bold) {
            $out .= "\x1B\x45\x01";
        }

        $out .= $text."\n";

        if ($double) {
            $out .= "\x1D\x21\x00";
        } elseif ($bold) {
            $out .= "\x1B\x45\x00";
        }

        return $out;
    }

    private function row(string $left, string $right, bool $bold = false): string
    {
        $left = mb_substr($left, 0, max(0, $this->width - mb_strlen($right) - 1));
        $padding = max(0, $this->width - mb_strlen($left) - mb_strlen($right));

        return $this->line($left.str_repeat(' ', $padding).$right, bold: $bold);
    }

    private function divider(): string
    {
        return $this->line(str_repeat('-', $this->width));
    }

    private function feed(int $lines): string
    {
        return "\x1B\x64".chr($lines);
    }

    private function cut(): string
    {
        return "\x1D\x56\x42\x00";
    }

    private function money(float|int|string $value): string
    {
        return number_format((float) $value, 2);
    }

    /**
     * @return array{0: bool, 1: string}
     */
    private function send(Printer $printer, string $bytes): array
    {
        if (($printer->connection_type ?? 'network') === 'local') {
            return $this->sendRawLocal($printer, $bytes);
        }

        return $this->sendNetwork($printer, $bytes);
    }

    /**
     * @return array{0: bool, 1: string}
     */
    private function sendNetwork(Printer $printer, string $bytes): array
    {
        $fp = @fsockopen($printer->ip_address, $printer->port, $errno, $errstr, 3);

        if ($fp === false) {
            $reason = $errstr !== '' ? $errstr : "No responde en {$printer->ip_address}:{$printer->port}.";

            return [false, "No se pudo conectar a la impresora {$printer->name}. {$reason}"];
        }

        stream_set_timeout($fp, 3);
        fwrite($fp, $bytes);
        fclose($fp);

        return [true, 'El ticket se envió a la impresora correctamente.'];
    }

    /**
     * @return array{0: bool, 1: string}
     */
    private function sendRawLocal(Printer $printer, string $bytes): array
    {
        $queue = trim((string) $printer->queue_name);

        if ($queue === '') {
            return [false, 'La impresora local no tiene nombre de cola (queue) configurado.'];
        }

        if (! function_exists('proc_open')) {
            return [false, 'No se puede imprimir localmente: proc_open no está disponible en este servidor.'];
        }

        $tmp = tempnam(sys_get_temp_dir(), 'ticket_');

        if ($tmp === false) {
            return [false, 'No se pudo crear el archivo temporal de impresión.'];
        }

        file_put_contents($tmp, $bytes);

        $process = @proc_open(
            ['lp', '-d', $queue, '-o', 'raw', $tmp],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );

        if (! is_resource($process)) {
            @unlink($tmp);

            return [false, 'No se pudo ejecutar el comando de impresión (lp). ¿Está instalado CUPS?'];
        }

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $code = proc_close($process);
        @unlink($tmp);

        if ($code !== 0) {
            $reason = trim((string) $stderr) ?: 'revisa que la cola exista y que el usuario tenga permisos de impresión.';

            return [false, "CUPS rechazó el trabajo: {$reason}"];
        }

        return [true, 'El ticket se envió a la impresora local correctamente.'];
    }

    /**
     * Envía un PDF a CUPS sin el modo crudo, para que el controlador de la
     * impresora (inkjet/láser) lo interprete correctamente.
     *
     * @return array{0: bool, 1: string}
     */
    private function sendDrivenPdf(Printer $printer, string $pdfContent): array
    {
        $queue = trim((string) $printer->queue_name);

        if ($queue === '') {
            return [false, 'La impresora local no tiene nombre de cola (queue) configurado.'];
        }

        if (! function_exists('proc_open')) {
            return [false, 'No se puede imprimir localmente: proc_open no está disponible en este servidor.'];
        }

        $tmp = tempnam(sys_get_temp_dir(), 'ticket_');

        if ($tmp === false) {
            return [false, 'No se pudo crear el archivo temporal de impresión.'];
        }

        $path = $tmp.'.pdf';
        file_put_contents($path, $pdfContent);

        $process = @proc_open(
            ['lp', '-d', $queue, $path],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );

        if (! is_resource($process)) {
            @unlink($tmp);
            @unlink($path);

            return [false, 'No se pudo ejecutar el comando de impresión (lp). ¿Está instalado CUPS?'];
        }

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $code = proc_close($process);
        @unlink($tmp);
        @unlink($path);

        if ($code !== 0) {
            $reason = trim((string) $stderr) ?: 'revisa que la cola exista y que el usuario tenga permisos de impresión.';

            return [false, "CUPS rechazó el trabajo: {$reason}"];
        }

        return [true, 'El comprobante se envió a la impresora local correctamente.'];
    }
}
