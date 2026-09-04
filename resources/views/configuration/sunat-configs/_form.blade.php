@php
    $selectedStatus = old('status', $sunatConfig->status ?? 'active');
@endphp

<div class="space-y-6">
    <div>
        <label for="company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Empresa</label>
        <select id="company_id" name="company_id" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <option value="">Seleccione una empresa</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected(old('company_id', $sunatConfig->company_id ?? '') == $company->id)>
                    {{ $company->name }}
                </option>
            @endforeach
        </select>
        @error('company_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de inicio</label>
            <input id="start_date" name="start_date" type="date"
                value="{{ old('start_date', isset($sunatConfig) ? $sunatConfig->start_date->format('Y-m-d') : '') }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            @error('start_date')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de fin</label>
            <input id="end_date" name="end_date" type="date"
                value="{{ old('end_date', isset($sunatConfig) ? $sunatConfig->end_date->format('Y-m-d') : '') }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            @error('end_date')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
        <select id="status" name="status" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <option value="active" @selected($selectedStatus === 'active')>Activo</option>
            <option value="inactive" @selected($selectedStatus === 'inactive')>Inactivo</option>
            <option value="expired" @selected($selectedStatus === 'expired')>Vencido</option>
        </select>
        @error('status')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="max_receipts" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tope de comprobantes</label>
            <input id="max_receipts" name="max_receipts" type="number" min="1"
                value="{{ old('max_receipts', $sunatConfig->max_receipts ?? '') }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            @error('max_receipts')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="used_receipts" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Comprobantes usados</label>
            <input id="used_receipts" name="used_receipts" type="number" min="0"
                value="{{ old('used_receipts', $sunatConfig->used_receipts ?? 0) }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            @error('used_receipts')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="card_surcharge_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Recargo por tarjeta (%)
        </label>
        <input id="card_surcharge_percentage" name="card_surcharge_percentage" type="number" step="0.01" min="0" max="100"
            value="{{ old('card_surcharge_percentage', $sunatConfig->card_surcharge_percentage ?? '0') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        @error('card_surcharge_percentage')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            Guardar
        </button>
        <a href="{{ route('configuration.sunat-configs.index') }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500 dark:text-gray-400">
            Cancelar
        </a>
    </div>
</div>