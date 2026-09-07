<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
        <input id="name" name="name" type="text" value="{{ old('name', $deliveryProvider->name ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', $deliveryProvider->phone ?? '') }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        @error('phone')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="contact_person" class="block text-sm font-medium text-gray-700">Persona de contacto</label>
        <input id="contact_person" name="contact_person" type="text" value="{{ old('contact_person', $deliveryProvider->contact_person ?? '') }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        @error('contact_person')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
            <input id="status" name="status" type="checkbox" value="1"
                {{ old('status', $deliveryProvider->status ?? true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            Activo
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
            Guardar
        </button>
        <a href="{{ route('restaurant.delivery-providers.index') }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500">
            Cancelar
        </a>
    </div>
</div>
