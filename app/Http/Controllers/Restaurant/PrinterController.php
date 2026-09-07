<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\StorePrinterRequest;
use App\Http\Requests\Restaurant\UpdatePrinterRequest;
use App\Models\Restaurant\Printer;
use App\Services\PrinterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrinterController extends Controller
{
    public function index(): View
    {
        $printers = Printer::orderByDesc('is_default')->latest('id')->paginate(10);

        return view('restaurant.printers.index', compact('printers'));
    }

    public function create(): View
    {
        [$scanFrom, $scanTo, $scanServer] = $this->lanRange();

        return view('restaurant.printers.create', compact('scanFrom', 'scanTo', 'scanServer'));
    }

    public function store(StorePrinterRequest $request): RedirectResponse
    {
        $data = $this->normalize($request->validated());

        if ($data['is_default']) {
            Printer::where('is_default', true)->update(['is_default' => false]);
        }

        Printer::create($data);

        return redirect()->route('restaurant.printers.index')
            ->with('success', 'Impresora creada correctamente.');
    }

    public function edit(Printer $printer): View
    {
        [$scanFrom, $scanTo, $scanServer] = $this->lanRange();

        return view('restaurant.printers.edit', compact('printer', 'scanFrom', 'scanTo', 'scanServer'));
    }

    public function update(UpdatePrinterRequest $request, Printer $printer): RedirectResponse
    {
        $data = $this->normalize($request->validated());

        if ($data['is_default']) {
            Printer::where('is_default', true)->whereKeyNot($printer->getKey())->update(['is_default' => false]);
        }

        $printer->update($data);

        return redirect()->route('restaurant.printers.index')
            ->with('success', 'Impresora actualizada correctamente.');
    }

    public function destroy(Printer $printer): RedirectResponse
    {
        if ($printer->is_default && Printer::where('is_default', true)->count() === 1) {
            return redirect()
                ->route('restaurant.printers.index')
                ->withErrors('La impresora de caja principal no se puede eliminar.');
        }

        $printer->delete();

        return redirect()->route('restaurant.printers.index')
            ->with('success', 'Impresora eliminada correctamente.');
    }

    public function test(Printer $printer, PrinterService $service): RedirectResponse
    {
        [$ok, $message] = $service->test($printer);

        if ($ok) {
            return redirect()->route('restaurant.printers.index')->with('success', $message);
        }

        return redirect()->route('restaurant.printers.index')->withErrors($message);
    }

    public function scan(Request $request, PrinterService $service): JsonResponse
    {
        $from = trim((string) $request->input('from', ''));
        $to = trim((string) $request->input('to', ''));

        $start = ip2long($from);
        $end = ip2long($to);

        if ($start === false || $end === false) {
            return response()->json(['message' => 'Ingresa un rango de IPs válido.'], 422);
        }
        if ($end < $start) {
            return response()->json(['message' => 'La IP inicial no puede ser mayor que la final.'], 422);
        }
        if ($end - $start > 254) {
            return response()->json(['message' => 'El rango no puede superar 255 IPs.'], 422);
        }

        foreach ([$start, $end] as $long) {
            if ($long >= ip2long('127.0.0.0') && $long <= ip2long('127.255.255.255')) {
                return response()->json(['message' => 'El rango 127.x.x.x es loopback (solo tu propia máquina). '
                    .'Usa la subred de la red local, p. ej. 192.168.1.1 - 192.168.1.254.'], 422);
            }
        }

        $printers = $service->discover($from, $to);

        return response()->json(['printers' => $printers]);
    }

    public function queues(): JsonResponse
    {
        $queues = [];

        @exec('lpstat -p 2>/dev/null', $output, $code);

        if ($code !== 0) {
            return response()->json(['message' => 'No se pudo consultar CUPS (lpstat). '
                .'Instala CUPS en el servidor: sudo apt install cups'], 422);
        }

        foreach ($output as $line) {
            if (preg_match('/^printer\s+(\S+)\s+/i', $line, $matches)) {
                $queues[] = $matches[1];
            }
        }

        if ($queues === []) {
            return response()->json(['message' => 'CUPS no tiene impresoras instaladas. '
                .'Conecta la impresora al equipo e instálala desde Configuración > Impresoras (o sudo lpadmin ...).'], 422);
        }

        return response()->json(['queues' => array_values(array_unique($queues))]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalize(array $data): array
    {
        $connectionType = $data['connection_type'] ?? 'network';
        $isNetwork = $connectionType === 'network';

        return [
            'name' => $data['name'],
            'connection_type' => $connectionType,
            'ip_address' => $isNetwork ? ($data['ip_address'] ?? null) : null,
            'port' => $isNetwork ? ($data['port'] ?? 9100) : 9100,
            'queue_name' => $isNetwork ? null : ($data['queue_name'] ?? null),
            'send_raw' => $this->isChecked($data, 'send_raw', true),
            'is_default' => $this->isChecked($data, 'is_default'),
            'is_active' => $this->isChecked($data, 'is_active', true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function isChecked(array $data, string $key, bool $default = false): bool
    {
        $field = $data[$key] ?? $default;

        return $field === true || $field === '1' || $field === 'on';
    }

    /**
     * Detecta el rango de la red local del servidor para prellenar el escaneo.
     * Nunca usa loopback: en Ubuntu el hostname resuelve a 127.0.1.1, que no es una red real.
     *
     * @return array{0: string, 1: string, 2: ?string}
     */
    private function lanRange(): array
    {
        $lanIp = $this->lanIP();

        if ($lanIp === null) {
            return ['192.168.1.1', '192.168.1.254', null];
        }

        $octets = explode('.', $lanIp);
        $base = "{$octets[0]}.{$octets[1]}.{$octets[2]}.";

        return [$base.'1', $base.'254', $lanIp];
    }

    /**
     * Obtiene la IP de red del servidor, priorizando rangos privados y evitando loopback.
     */
    private function lanIP(): ?string
    {
        $candidates = [];

        @exec('hostname -I 2>/dev/null', $output, $code);

        if ($code === 0) {
            $candidates = preg_split('/\s+/', trim((string) implode(' ', $output))) ?: [];
        }

        $fallback = [];

        foreach ($candidates as $ip) {
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || str_starts_with($ip, '127.')) {
                continue;
            }
            if ($this->isPrivateRange(ip2long($ip))) {
                return $ip;
            }
            $fallback[] = $ip;
        }

        if ($fallback !== []) {
            return $fallback[0];
        }

        $ip = @gethostbyname(gethostname());
        $long = is_string($ip) ? ip2long($ip) : false;

        if ($long !== false && $long !== 0 && $long !== ip2long('127.0.0.1') && $this->isPrivateRange($long)) {
            return $ip;
        }

        return null;
    }

    private function isPrivateRange(int $long): bool
    {
        return ($long >= ip2long('10.0.0.0') && $long <= ip2long('10.255.255.255'))
            || ($long >= ip2long('172.16.0.0') && $long <= ip2long('172.31.255.255'))
            || ($long >= ip2long('192.168.0.0') && $long <= ip2long('192.168.255.255'));
    }
}
