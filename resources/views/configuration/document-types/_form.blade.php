@php
    $selectedType = old('type', $documentType->type ?? 'identification');
@endphp

<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
        <input id="name" name="name" type="text" value="{{ old('name', $documentType->name ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        @error('name')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="nomenclature" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sigla</label>
            <input id="nomenclature" name="nomenclature" type="text" value="{{ old('nomenclature', $documentType->nomenclature ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>

        <div>
            <label for="character_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Límite de caracteres</label>
            <input id="character_limit" name="character_limit" type="number" min="1"
                value="{{ old('character_limit', $documentType->character_limit ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>
    </div>

    <div>
        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo</label>
        <select id="type" name="type" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <option value="identification" @selected($selectedType === 'identification')>Identificación</option>
            <option value="invoice" @selected($selectedType === 'invoice')>Comprobante</option>
        </select>
        @error('type')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
            <input id="status" name="status" type="checkbox" value="1"
                {{ old('status', $documentType->status ?? true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800">
            Activo
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            Guardar
        </button>
        <a href="{{ route('configuration.document-types.index') }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500 dark:text-gray-400">
            Cancelar
        </a>
    </div>
</div>
