@extends('layouts.app')

@section('title', 'Nueva compra')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nueva compra</h1>

    <div class="mt-6 max-w-3xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <form action="{{ route('inventory.purchases.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Proveedor</label>
                    <select id="supplier_id" name="supplier_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                        <option value="">Seleccione proveedor</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->social_reason }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="document_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Comprobante</label>
                    <select id="document_type_id" name="document_type_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                        <option value="">Seleccione comprobante</option>
                        @foreach ($documentTypes as $documentType)
                            <option value="{{ $documentType->id }}" @selected(old('document_type_id') == $documentType->id)>{{ $documentType->name }}</option>
                        @endforeach
                    </select>
                    @error('document_type_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4 grid grid-cols-3 gap-4">
                <div>
                    <label for="purchase_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de compra</label>
                    <select id="purchase_type" name="purchase_type" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                        <option value="contado" @selected(old('purchase_type', 'contado') === 'contado')>Contado</option>
                        <option value="credito" @selected(old('purchase_type') === 'credito')>Crédito</option>
                    </select>
                </div>

                <div>
                    <label for="series" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Serie</label>
                    <input id="series" name="series" type="text" value="{{ old('series') }}" placeholder="F001"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                </div>

                <div>
                    <label for="number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número</label>
                    <input id="number" name="number" type="text" value="{{ old('number') }}" placeholder="000123" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                    @error('number')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label for="purchase_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de compra</label>
                <input id="purchase_date" name="purchase_date" type="date" value="{{ old('purchase_date', now()->format('Y-m-d')) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                @error('purchase_date')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Productos</label>
                    <button type="button" data-add-line
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">+ Agregar producto</button>
                </div>
                <div data-lines-list class="mt-2 space-y-2">
                    @php $lines = old('details', []); @endphp
                    @forelse ($lines as $index => $line)
                        <div data-line-row class="flex items-center gap-2">
                            <select name="details[{{ $index }}][product_id]" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                <option value="">Seleccione producto</option>
                                @foreach ($supplies as $supply)
                                    <option value="{{ $supply->id }}" @selected($line['product_id'] == $supply->id)>{{ $supply->name }}</option>
                                @endforeach
                            </select>
                            <input type="number" step="0.01" min="0" name="details[{{ $index }}][quantity]" placeholder="Cant." required
                                value="{{ $line['quantity'] ?? '' }}"
                                class="w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                            <input type="number" step="0.01" min="0" name="details[{{ $index }}][unit_price]" placeholder="P. unit." required
                                value="{{ $line['unit_price'] ?? '' }}"
                                class="w-28 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                            <button type="button" data-remove-line
                                class="text-sm font-medium text-red-600 hover:text-red-500 dark:text-red-400">Quitar</button>
                        </div>
                    @empty
                        <p data-lines-empty class="text-sm text-gray-500 dark:text-gray-400">Agregue al menos un producto a la compra.</p>
                    @endforelse
                </div>
                @error('details')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Registrar compra
                </button>
                <a href="{{ route('inventory.purchases.index') }}"
                    class="text-sm font-medium text-gray-600 hover:text-gray-500 dark:text-gray-400">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const lineOption = (index) => `
            <select name="details[${index}][product_id]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <option value="">Seleccione producto</option>
                @foreach ($supplies as $supply)
                    <option value="{{ $supply->id }}">{{ $supply->name }}</option>
                @endforeach
            </select>
            <input type="number" step="0.01" min="0" name="details[${index}][quantity]" placeholder="Cant." required class="w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <input type="number" step="0.01" min="0" name="details[${index}][unit_price]" placeholder="P. unit." required class="w-28 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <button type="button" data-remove-line class="text-sm font-medium text-red-600 hover:text-red-500 dark:text-red-400">Quitar</button>
        `;

        document.querySelector('[data-add-line]').addEventListener('click', () => {
            const list = document.querySelector('[data-lines-list]');
            const emptyHint = list.querySelector('[data-lines-empty]');
            if (emptyHint) emptyHint.remove();
            const index = list.querySelectorAll('[data-line-row]').length;
            const row = document.createElement('div');
            row.setAttribute('data-line-row', '');
            row.className = 'flex items-center gap-2';
            row.innerHTML = lineOption(index);
            list.appendChild(row);
        });

        document.querySelector('[data-lines-list]').addEventListener('click', (event) => {
            if (event.target.closest('[data-remove-line]')) {
                event.target.closest('[data-line-row]').remove();
            }
        });
    </script>
@endpush