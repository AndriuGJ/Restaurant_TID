<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
        <input id="name" name="name" type="text" value="{{ old('name', $productCategory->name ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
        <textarea id="description" name="description" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('description', $productCategory->description ?? '') }}</textarea>
    </div>

    <div>
        <label for="status" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
            <input id="status" name="status" type="checkbox" value="1"
                {{ old('status', $productCategory->status ?? true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            Activo
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
            Guardar
        </button>
        <a href="{{ route('inventory.product-categories.index') }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500">
            Cancelar
        </a>
    </div>
</div>