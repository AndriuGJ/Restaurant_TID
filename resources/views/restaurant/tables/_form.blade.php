@php
    $selectedHall = old('hall_id', $table->hall_id ?? null);
    $selectedShape = old('shape', $table->shape ?? 'square');
    $selectedStatus = old('status', $table->status ?? 'available');
@endphp

<div class="space-y-6">
    <div>
        <label for="hall_id" class="block text-sm font-medium text-gray-700">Salón</label>
        <select id="hall_id" name="hall_id" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Seleccione un salón</option>
            @foreach ($halls as $hall)
                <option value="{{ $hall->id }}" @selected($selectedHall == $hall->id)>{{ $hall->name }}</option>
            @endforeach
        </select>
        @error('hall_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nombre de la mesa</label>
        <input id="name" name="name" type="text" value="{{ old('name', $table->name ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="shape" class="block text-sm font-medium text-gray-700">Forma</label>
        <select id="shape" name="shape" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="square" @selected($selectedShape === 'square')>Cuadrada</option>
            <option value="round" @selected($selectedShape === 'round')>Redonda</option>
            <option value="rectangular" @selected($selectedShape === 'rectangular')>Rectangular</option>
        </select>
        @error('shape')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
        <select id="status" name="status" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="available" @selected($selectedStatus === 'available')>Disponible</option>
            <option value="occupied" @selected($selectedStatus === 'occupied')>Ocupada</option>
            <option value="reserved" @selected($selectedStatus === 'reserved')>Reservada</option>
        </select>
        @error('status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
            Guardar
        </button>
        <a href="{{ route('restaurant.tables.index') }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500">
            Cancelar
        </a>
    </div>
</div>
