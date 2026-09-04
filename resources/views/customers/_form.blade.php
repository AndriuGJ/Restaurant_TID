<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre completo</label>
        <input id="name" name="name" type="text" value="{{ old('name', $customer->name ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        @error('name')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone', $customer->phone ?? '') }}" required maxlength="9"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo</label>
            <input id="email" name="email" type="email" value="{{ old('email', $customer->email ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="reference_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dirección de referencia</label>
        <textarea id="reference_address" name="reference_address" rows="2"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">{{ old('reference_address', $customer->reference_address ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="document_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de documento</label>
            <select id="document_type_id" name="document_type_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <option value="">Ninguno</option>
                @foreach ($documentTypes as $documentType)
                    <option value="{{ $documentType->id }}" @selected(old('document_type_id', $customer->document_type_id ?? '') == $documentType->id)>
                        {{ $documentType->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="document_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">N.º de documento</label>
            <input id="document_number" name="document_number" type="text" value="{{ old('document_number', $customer->document_number ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>
    </div>

    <div>
        <label for="status" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
            <input id="status" name="status" type="checkbox" value="1"
                {{ old('status', $customer->status ?? true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800">
            Activo
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            Guardar
        </button>
        <a href="{{ route('customers.index') }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500 dark:text-gray-400">
            Cancelar
        </a>
    </div>
</div>
