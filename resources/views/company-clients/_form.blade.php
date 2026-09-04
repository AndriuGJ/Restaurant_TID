<div class="space-y-6">
    <div>
        <label for="ruc" class="block text-sm font-medium text-gray-700 dark:text-gray-300">RUC</label>
        <input id="ruc" name="ruc" type="text" value="{{ old('ruc', $companyClient->ruc ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        @error('ruc')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="social_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Razón social</label>
        <input id="social_reason" name="social_reason" type="text" value="{{ old('social_reason', $companyClient->social_reason ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        @error('social_reason')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone', $companyClient->phone ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>

        <div>
            <label for="contact_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Persona de contacto</label>
            <input id="contact_person" name="contact_person" type="text" value="{{ old('contact_person', $companyClient->contact_person ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>
    </div>

    <div>
        <label for="document_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de documento</label>
        <select id="document_type_id" name="document_type_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <option value="">Ninguno</option>
            @foreach ($documentTypes as $documentType)
                <option value="{{ $documentType->id }}" @selected(old('document_type_id', $companyClient->document_type_id ?? '') == $documentType->id)>
                    {{ $documentType->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="status" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
            <input id="status" name="status" type="checkbox" value="1"
                {{ old('status', $companyClient->status ?? true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800">
            Activo
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            Guardar
        </button>
        <a href="{{ route('customers.companies.index') }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500 dark:text-gray-400">
            Cancelar
        </a>
    </div>
</div>