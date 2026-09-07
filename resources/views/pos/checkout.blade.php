@extends('layouts.app')

@section('title', 'Cobro')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cobro — Pedido #{{ $sale->id }}</h1>
            <p class="mt-1 text-sm text-gray-600">
                @if ($sale->sale_type === 'delivery')
                    DELIVERY
                @elseif ($sale->sale_type === 'quick_sale')
                    PARA LLEVAR
                @else
                    Mesa <span class="font-semibold">{{ $sale->table?->name ?? '—' }}</span>
                @endif
            </p>
        </div>
        <a href="{{ route('pos.sale', $sale) }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500">← Volver al pedido</a>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Resumen --}}
        <div class="rounded-md border border-gray-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Resumen</h2>
            <ul class="mt-3 space-y-2">
                @foreach ($sale->details as $detail)
                    <li class="flex justify-between text-sm">
                        <span class="text-gray-700">{{ $detail->product?->name }} × {{ format_quantity($detail->quantity) }}</span>
                        <span class="font-medium text-gray-900">S/ {{ number_format($detail->subtotal, 2) }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="mt-4 space-y-1 border-t border-gray-200 pt-3">
                <div class="flex justify-between text-sm font-bold text-gray-900">
                    <span>Total a pagar</span>
                    <span id="sale-total" data-total="{{ $sale->total }}">S/ {{ number_format($sale->total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Formulario de cobro --}}
        <div class="rounded-md border border-gray-200 bg-white p-4 shadow-sm">
            <form method="POST" action="{{ route('pos.pay', $sale) }}">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cliente</label>
                        <select name="clientable_type" id="client-type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">Público general</option>
                            <option value="customer">Cliente (persona)</option>
                            <option value="company">Empresa</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Comprobante</label>
                        <select name="document_type_id" id="document-type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">Sin comprobante</option>
                            @foreach ($documentTypes as $documentType)
                                <option value="{{ $documentType->id }}">{{ $documentType->name }}</option>
                            @endforeach
                        </select>
                        <p id="document-hint" class="mt-1 text-xs text-gray-500">
                            La boleta se emite sin RUC. La factura requiere una empresa (RUC) registrada en Clientes.
                        </p>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Seleccionar cliente</label>
                    <select name="clientable_id" id="client-select" disabled
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </select>
                    <p id="client-hint" class="mt-1 text-xs text-amber-600"></p>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Comensales</label>
                    <div class="mt-1 flex w-[8.5rem] items-center overflow-hidden rounded-md border border-gray-300 shadow-sm">
                        <button type="button" data-guests-minus aria-label="Restar un comensal"
                            class="flex h-8 w-8 items-center justify-center text-gray-600 transition hover:bg-gray-100 hover:text-brand-600 active:bg-gray-200">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" /></svg>
                        </button>
                        <input type="number" name="guests" id="guests" value="{{ $sale->guests }}" min="1"
                            class="h-8 w-10 border-x border-gray-300 text-center text-sm font-semibold text-gray-900 focus:border-brand-500 focus:ring-brand-500 [-moz-appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <button type="button" data-guests-plus aria-label="Sumar un comensal"
                            class="flex h-8 w-8 items-center justify-center bg-brand-500 text-white transition hover:bg-brand-600 active:bg-brand-700">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        </button>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-medium text-gray-700">Pagos (puedes dividir)</label>
                        <button type="button" id="add-payment"
                            class="text-sm font-medium text-brand-600 hover:text-brand-500">+ Agregar pago</button>
                    </div>
                    <div id="payments-list" class="mt-2 space-y-2">
                        <div data-payment-row class="flex items-center gap-2">
                            <select name="payments[0][payment_method_id]" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="">Medio de pago</option>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                            <input type="number" step="0.01" min="0.01" name="payments[0][amount]" placeholder="Monto" required
                                class="payment-amount w-32 rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            <button type="button" data-remove-payment
                                class="text-sm font-medium text-red-600 hover:text-red-500">Quitar</button>
                        </div>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-medium text-gray-500">Monto rápido:</span>
                        @foreach ([20, 50, 100, 200] as $amount)
                            <button type="button" data-quick-amount="{{ $amount }}"
                                class="rounded-md border border-gray-200 bg-gray-50 px-3 py-1 text-sm font-semibold text-gray-700 transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700">
                                S/ {{ $amount }}
                            </button>
                        @endforeach
                    </div>
                    <p id="payments-sum" class="mt-2 text-sm text-gray-500"></p>
                    @error('payments')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="mt-6 w-full rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                    Confirmar pago
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const typeSelect = document.getElementById('client-type');
            const clientSelect = document.getElementById('client-select');
            const docSelect = document.getElementById('document-type');
            const docHint = document.getElementById('document-hint');
            const clientHint = document.getElementById('client-hint');

            const guestsInput = document.getElementById('guests');
            const clampGuests = () => {
                let v = parseInt(guestsInput.value, 10);
                if (isNaN(v) || v < 1) v = 1;
                guestsInput.value = v;
            };
            document.querySelector('[data-guests-minus]').addEventListener('click', () => {
                guestsInput.value = Math.max(1, (parseInt(guestsInput.value, 10) || 1) - 1);
            });
            document.querySelector('[data-guests-plus]').addEventListener('click', () => {
                guestsInput.value = (parseInt(guestsInput.value, 10) || 1) + 1;
            });
            guestsInput.addEventListener('change', clampGuests);
            guestsInput.addEventListener('blur', clampGuests);

            const clients = {
                customer: @json(\App\Models\Customers\Customer::orderBy('name')->get(['id', 'name'])),
                company: @json(\App\Models\Customers\CompanyClient::orderBy('social_reason')->get(['id', 'social_reason'])),
            };

            const documents = @json($documentTypes->map(fn ($doc) => ['id' => $doc->id, 'name' => $doc->name, 'nomenclature' => $doc->nomenclature])->values());

            const fillClients = () => {
                const type = typeSelect.value;
                clientSelect.innerHTML = '<option value="">Seleccione</option>';
                clientSelect.disabled = !type;
                if (!type) return;
                const list = clients[type] || [];
                list.forEach((item) => {
                    const label = type === 'company' ? item.social_reason : item.name;
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = label;
                    clientSelect.appendChild(opt);
                });
            };

            typeSelect.addEventListener('change', () => {
                fillClients();
                clientHint.textContent = typeSelect.value === 'company'
                    ? 'Se requiere una empresa (RUC) ya registrada en Clientes.'
                    : '';
            });

            const refreshDocGuidance = () => {
                const selected = documents.find((d) => String(d.id) === String(docSelect.value));
                if (selected && selected.nomenclature && selected.nomenclature.toUpperCase() === 'F') {
                    docHint.textContent = 'Factura: requiere una empresa (RUC) registrada en Clientes.';
                    clientHint.textContent = 'Seleccione Empresa para poder emitir la factura.';
                } else if (selected) {
                    docHint.textContent = 'Boleta: se emite sin RUC.';
                    clientHint.textContent = typeSelect.value === 'company'
                        ? 'Se requiere una empresa (RUC) ya registrada en Clientes.'
                        : '';
                } else {
                    docHint.textContent = 'La boleta se emite sin RUC. La factura requiere una empresa (RUC) registrada en Clientes.';
                    clientHint.textContent = '';
                }
            };

            docSelect.addEventListener('change', refreshDocGuidance);

            const total = parseFloat(document.getElementById('sale-total').dataset.total);
            const sumLabel = document.getElementById('payments-sum');
            const recalc = () => {
                let sum = 0;
                document.querySelectorAll('[data-payment-row] input[name$="[amount]"]').forEach((inp) => {
                    const v = parseFloat(inp.value);
                    if (!isNaN(v)) sum += v;
                });
                const diff = sum - total;
                let text = `Total pagado: S/ ${sum.toFixed(2)}`;
                if (diff < -0.001) {
                    text += ` — falta S/ ${(-diff).toFixed(2)}`;
                } else if (diff > 0.001) {
                    text += ` — vuelto S/ ${diff.toFixed(2)}`;
                } else {
                    text += ' — monto exacto';
                }
                sumLabel.textContent = text;
                sumLabel.className = 'mt-2 text-sm ' + (diff < -0.001 ? 'text-red-600' : 'text-green-600');
            };

            document.querySelectorAll('[data-quick-amount]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const active = document.activeElement && document.activeElement.classList;
                    const target = active && active.contains('payment-amount')
                        ? document.activeElement
                        : document.querySelector('.payment-amount');
                    if (!target) return;
                    target.value = btn.dataset.quickAmount;
                    recalc();
                });
            });

            document.getElementById('add-payment').addEventListener('click', () => {
                const list = document.getElementById('payments-list');
                const index = list.querySelectorAll('[data-payment-row]').length;
                const row = document.createElement('div');
                row.setAttribute('data-payment-row', '');
                row.className = 'flex items-center gap-2';
                row.innerHTML = `\
<select name="payments[${index}][payment_method_id]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">\
@foreach ($paymentMethods as $method)\
<option value="{{ $method->id }}">{{ $method->name }}</option>\
@endforeach\
</select>\
<input type="number" step="0.01" min="0.01" name="payments[${index}][amount]" placeholder="Monto" required class="payment-amount w-32 rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">\
<button type="button" data-remove-payment class="text-sm font-medium text-red-600 hover:text-red-500">Quitar</button>\
`;
                list.appendChild(row);
                row.querySelector('[data-remove-payment]').addEventListener('click', () => { row.remove(); recalc(); });
            });

            document.getElementById('payments-list').addEventListener('input', recalc);
            document.getElementById('payments-list').addEventListener('click', (e) => {
                if (e.target.closest('[data-remove-payment]')) {
                    e.target.closest('[data-payment-row]').remove();
                    recalc();
                }
            });
            recalc();
            refreshDocGuidance();
        })();
    </script>
@endpush