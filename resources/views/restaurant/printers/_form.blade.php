<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nombre de la impresora</label>
        <input id="name" name="name" type="text" value="{{ old('name', $printer->name ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="connection_type" class="block text-sm font-medium text-gray-700">Tipo de conexión</label>
        <select id="connection_type" name="connection_type"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="network" @selected(old('connection_type', $printer->connection_type ?? 'network') === 'network')>
                Red (LAN / WiFi)
            </option>
            <option value="local" @selected(old('connection_type', $printer->connection_type ?? 'network') === 'local')>
                USB / Cable directo (local, CUPS)
            </option>
        </select>
    </div>

    <div id="net-fields">
        <div>
            <label for="ip_address" class="block text-sm font-medium text-gray-700">Dirección IP de red</label>
            <input id="ip_address" name="ip_address" type="text" value="{{ old('ip_address', $printer->ip_address ?? '') }}"
                placeholder="192.168.1.50"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            @error('ip_address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Es la IP de la impresora térmica (no la del router ni la de la computadora).
                La encuentras en el menú de red de la impresora o con el buscador de aquí abajo.</p>
        </div>

        <div class="mt-4 rounded-md border border-gray-200 bg-gray-50 p-4">
            <p class="text-sm font-medium text-gray-700">Buscar impresoras en la red</p>
            <p class="mt-1 text-xs text-gray-500">
                Escanea el rango de IPs y detecta cuáles tienen el puerto de impresión (9100) abierto.
                @if ($scanServer)
                    El análisis sale desde el servidor ({{ $scanServer }}).
                @endif
            </p>

            <div class="mt-3 flex items-center gap-2">
                <input id="scan_from" type="text" value="{{ $scanFrom }}" aria-label="IP inicial"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <span class="text-gray-400">→</span>
                <input id="scan_to" type="text" value="{{ $scanTo }}" aria-label="IP final"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <button type="button" id="btn-scan"
                    class="shrink-0 rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                    Buscar
                </button>
            </div>

            <p id="scan-status" class="mt-2 hidden text-sm text-gray-600"></p>
            <ul id="scan-results" class="mt-3 space-y-1"></ul>
        </div>

        <div class="mt-4">
            <label for="port" class="block text-sm font-medium text-gray-700">Puerto</label>
            <input id="port" name="port" type="number" min="1" max="65535" value="{{ old('port', $printer->port ?? 9100) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            @error('port')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">El 9100 es el puerto estándar RAW de impresoras térmicas (ESC/POS).</p>
        </div>
    </div>

    <div id="local-fields" class="hidden">
        <div>
            <label for="queue_name" class="block text-sm font-medium text-gray-700">Nombre de cola local (queue)</label>
            <div class="mt-1 flex items-center gap-2">
                <input id="queue_name" name="queue_name" type="text" value="{{ old('queue_name', $printer->queue_name ?? '') }}"
                    placeholder="EPSON_TM-T20II"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <button type="button" id="btn-queues"
                    class="shrink-0 rounded-md bg-slate-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                    Detectar colas CUPS
                </button>
            </div>
            @error('queue_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p id="queues-status" class="mt-2 hidden text-sm"></p>
            <div id="queues-results" class="mt-2 flex flex-wrap gap-2"></div>
            <p class="mt-1 text-xs text-gray-500">
                Es la cola que ve el sistema del equipo (CUPS). El botón consulta las colas instaladas en el servidor.
                El usuario del servidor debe tener permiso de impresión (grupo lpadmin).
                Puedes guardar sin llenarla y configurarla después; solo hace falta para imprimir.
            </p>
        </div>

        <div class="mt-4 rounded-md border border-gray-200 bg-gray-50 p-4">
            <label for="send_raw" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                <input id="send_raw" name="send_raw" type="checkbox" value="1"
                    {{ old('send_raw', $printer->send_raw ?? true) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                Impresora térmica (ESC/POS, envío crudo)
            </label>
            <p class="mt-1 text-xs text-gray-500">
                <b>Marcado:</b> térmica de tickets (ej. XP-80, TM-T20II). <b>Sin marcar:</b> impresora común
                (inkjet o láser, ej. Epson L3250) — se imprime un PDF usando su controlador de CUPS.
            </p>
        </div>
    </div>

    <div>
        <label for="is_default" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
            <input id="is_default" name="is_default" type="checkbox" value="1"
                {{ old('is_default', $printer->is_default ?? false) ? 'checked' : '' }}
                class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
            Caja principal
        </label>
        <p class="mt-1 text-xs text-gray-500">Los comprobantes de venta se imprimen automáticamente en esta impresora.</p>
    </div>

    <div>
        <label for="is_active" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
            <input id="is_active" name="is_active" type="checkbox" value="1"
                {{ old('is_active', $printer->is_active ?? true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
            Activa
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
            Guardar
        </button>
        <a href="{{ route('restaurant.printers.index') }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500">
            Cancelar
        </a>
    </div>
</div>

<script>
    (function () {
        const conn = document.getElementById('connection_type');
        const netFields = document.getElementById('net-fields');
        const localFields = document.getElementById('local-fields');
        const ipInput = document.getElementById('ip_address');

        function toggleConnection() {
            const isLocal = conn && conn.value === 'local';
            if (netFields) netFields.classList.toggle('hidden', isLocal);
            if (localFields) localFields.classList.toggle('hidden', !isLocal);
        }

        if (conn) {
            conn.addEventListener('change', toggleConnection);
            toggleConnection();
        }

        const btn = document.getElementById('btn-scan');
        const from = document.getElementById('scan_from');
        const to = document.getElementById('scan_to');
        const status = document.getElementById('scan-status');
        const list = document.getElementById('scan-results');
        const csrf = document.querySelector('meta[name="csrf-token"]');

        if (!btn || !from || !to || !status || !list || !ipInput || !csrf) return;

        function show(text, color) {
            status.textContent = text;
            status.className = 'mt-2 text-sm ' + color;
        }

        btn.addEventListener('click', function () {
            list.innerHTML = '';
            btn.disabled = true;
            show('Analizando la red...', 'text-gray-600');

            fetch('{{ route('restaurant.printers.scan') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf.content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ from: from.value.trim(), to: to.value.trim() }),
            })
                .then(function (res) {
                    return res.json().then(function (body) {
                        return { ok: res.ok, body: body };
                    });
                })
                .then(function (res) {
                    if (!res.ok) {
                        show(res.body.message || 'No se pudo analizar la red.', 'text-red-600');
                        return;
                    }
                    if (res.body.printers.length === 0) {
                        show('No se encontraron impresoras en el rango. Revisa que esté en la misma red WiFi.',
                            'text-yellow-600');
                        return;
                    }
                    show(res.body.printers.length + ' impresora(s) encontrada(s).', 'text-green-600');

                    res.body.printers.forEach(function (printer) {
                        const li = document.createElement('li');
                        li.className = 'flex items-center justify-between gap-3 rounded-md border border-gray-200 bg-white px-3 py-2';

                        const info = document.createElement('span');
                        info.className = 'text-sm text-gray-700';
                        let label = printer.ip;
                        if (printer.host) {
                            label += ' · ' + printer.host;
                        }
                        if (printer.model) {
                            label += ' · Impresora: ' + printer.model;
                        }
                        info.textContent = label;

                        const use = document.createElement('button');
                        use.type = 'button';
                        use.className = 'rounded-md bg-brand-500 px-3 py-1 text-xs font-semibold text-white hover:bg-brand-600';
                        use.textContent = 'Usar';
                        use.addEventListener('click', function () {
                            ipInput.value = printer.ip;
                            list.innerHTML = '';
                            show('IP ' + printer.ip + ' seleccionada.', 'text-green-600');
                        });

                        li.appendChild(info);
                        li.appendChild(use);
                        list.appendChild(li);
                    });
                })
                .catch(function () {
                    show('Error al analizar la red.', 'text-red-600');
                })
                .then(function () {
                    btn.disabled = false;
                });
        });

        const btnQueues = document.getElementById('btn-queues');
        const queuesStatus = document.getElementById('queues-status');
        const queuesResults = document.getElementById('queues-results');
        const queueInput = document.getElementById('queue_name');

        if (btnQueues && queuesStatus && queuesResults && queueInput && csrf) {
            function showQueues(text, color) {
                queuesStatus.textContent = text;
                queuesStatus.className = 'mt-2 text-sm ' + color;
            }

            btnQueues.addEventListener('click', function () {
                queuesResults.innerHTML = '';
                btnQueues.disabled = true;
                showQueues('Consultando CUPS...', 'text-gray-600');

                fetch('{{ route('restaurant.printers.queues') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf.content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                })
                    .then(function (res) {
                        return res.json().then(function (body) {
                            return { ok: res.ok, body: body };
                        });
                    })
                    .then(function (res) {
                        if (!res.ok) {
                            showQueues(res.body.message || 'No se pudo consultar CUPS.', 'text-red-600');
                            return;
                        }
                        if (res.body.queues.length === 0) {
                            showQueues('CUPS no tiene impresoras instaladas.', 'text-yellow-600');
                            return;
                        }
                        showQueues(res.body.queues.length + ' cola(s) disponible(s). Presiona una para usarla.', 'text-green-600');

                        res.body.queues.forEach(function (queue) {
                            const chip = document.createElement('button');
                            chip.type = 'button';
                            chip.className = 'rounded-md border border-gray-300 bg-white px-3 py-1 font-mono text-xs text-gray-700 hover:border-brand-500 hover:text-brand-600';
                            chip.textContent = queue;
                            chip.addEventListener('click', function () {
                                queueInput.value = queue;
                                showQueues('Cola "' + queue + '" seleccionada.', 'text-green-600');
                            });
                            queuesResults.appendChild(chip);
                        });
                    })
                    .catch(function () {
                        showQueues('Error al consultar CUPS.', 'text-red-600');
                    })
                    .then(function () {
                        btnQueues.disabled = false;
                    });
            });
        }
    })();
</script>