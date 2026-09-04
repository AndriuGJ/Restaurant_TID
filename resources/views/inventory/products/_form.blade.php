<div class="space-y-5">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
            <input id="name" name="name" type="text" value="{{ old('name', $product->name ?? '') }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo</label>
            <select id="type" name="type" data-type-target="ingredients-section"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                @php $currentType = old('type', $product->type ?? 'dish'); @endphp
                <option value="dish" @selected($currentType === 'dish')>Plato</option>
                <option value="supply" @selected($currentType === 'supply')>Insumo</option>
                <option value="combo" @selected($currentType === 'combo')>Combo</option>
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
        <textarea id="description" name="description" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="product_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoría de producto</label>
            <select id="product_category_id" name="product_category_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <option value="">Sin categoría</option>
                @foreach ($productCategories as $category)
                    <option value="{{ $category->id }}" @selected(old('product_category_id', $product->product_category_id ?? '') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="purchase_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoría de compra</label>
            <select id="purchase_category_id" name="purchase_category_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <option value="">Sin categoría</option>
                @foreach ($purchaseCategories as $category)
                    <option value="{{ $category->id }}" @selected(old('purchase_category_id', $product->purchase_category_id ?? '') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label for="sale_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio de venta (S/)</label>
            <input id="sale_price" name="sale_price" type="number" step="0.01" min="0" value="{{ old('sale_price', $product->sale_price ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>

        <div>
            <label for="cost_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Costo (S/)</label>
            <input id="cost_price" name="cost_price" type="number" step="0.01" min="0" value="{{ old('cost_price', $product->cost_price ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>

        <div>
            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio POS (S/)</label>
            <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock</label>
            <input id="stock" name="stock" type="number" step="0.01" min="0" value="{{ old('stock', $product->stock ?? 0) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>

        <div>
            <label for="unit_of_measure" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unidad de medida</label>
            <input id="unit_of_measure" name="unit_of_measure" type="text" value="{{ old('unit_of_measure', $product->unit_of_measure ?? 'unidad') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        </div>
    </div>

    <div>
        <label for="image_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">URL de imagen</label>
        <input id="image_url" name="image_url" type="url" value="{{ old('image_url', $product->image_url ?? '') }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
        @error('image_url')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div id="ingredients-section">
        <div class="flex items-center justify-between">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ingredientes</label>
            <button type="button" data-add-ingredient
                class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">+ Agregar insumo</button>
        </div>
        <div data-ingredients-list class="mt-2 space-y-2">
            @php $ingredientRows = old('ingredients', $product->ingredients ?? []); @endphp
            @forelse ($ingredientRows as $row)
                <div data-ingredient-row class="flex items-center gap-2">
                    <select name="ingredients[{{ $loop->index }}][ingredient_id]"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                        <option value="">Seleccione insumo</option>
                        @foreach ($supplies as $supply)
                            <option value="{{ $supply->id }}" @selected($row['ingredient_id'] == $supply->id)>{{ $supply->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" step="0.001" min="0" name="ingredients[{{ $loop->index }}][quantity]" placeholder="Cantidad"
                        value="{{ $row['quantity'] ?? '' }}"
                        class="w-28 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                    <button type="button" data-remove-ingredient
                        class="text-sm font-medium text-red-600 hover:text-red-500 dark:text-red-400">Quitar</button>
                </div>
            @empty
                <p data-ingredients-empty class="text-sm text-gray-500 dark:text-gray-400">Este plato no tiene ingredientes definidos.</p>
            @endforelse
        </div>
        @error('ingredients')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-6">
        <label for="is_pos_item" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
            <input id="is_pos_item" name="is_pos_item" type="checkbox" value="1"
                {{ old('is_pos_item', $product->is_pos_item ?? true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800">
            Visible en POS
        </label>

        <label for="status" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
            <input id="status" name="status" type="checkbox" value="1"
                {{ old('status', $product->status ?? true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800">
            Activo
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            Guardar
        </button>
        <a href="{{ route('inventory.products.index') }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500 dark:text-gray-400">
            Cancelar
        </a>
    </div>
</div>

@push('scripts')
    <script>
        const ingredientOption = (index) => `
            <select name="ingredients[${index}][ingredient_id]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <option value="">Seleccione insumo</option>
                @foreach ($supplies as $supply)
                    <option value="{{ $supply->id }}">{{ $supply->name }}</option>
                @endforeach
            </select>
            <input type="number" step="0.001" min="0" name="ingredients[${index}][quantity]" placeholder="Cantidad" class="w-28 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <button type="button" data-remove-ingredient class="text-sm font-medium text-red-600 hover:text-red-500 dark:text-red-400">Quitar</button>
        `;

        const ingredientsSection = document.getElementById('ingredients-section');
        const typeSelect = document.getElementById('type');

        const syncIngredientsVisibility = () => {
            ingredientsSection.style.display = typeSelect.value === 'dish' ? '' : 'none';
        };

        typeSelect.addEventListener('change', syncIngredientsVisibility);
        syncIngredientsVisibility();

        document.querySelector('[data-add-ingredient]').addEventListener('click', () => {
            const list = document.querySelector('[data-ingredients-list]');
            const emptyHint = list.querySelector('[data-ingredients-empty]');
            if (emptyHint) emptyHint.remove();
            const index = list.querySelectorAll('[data-ingredient-row]').length;
            const row = document.createElement('div');
            row.setAttribute('data-ingredient-row', '');
            row.className = 'flex items-center gap-2';
            row.innerHTML = ingredientOption(index);
            list.appendChild(row);
        });

        document.querySelector('[data-ingredients-list]').addEventListener('click', (event) => {
            if (event.target.closest('[data-remove-ingredient]')) {
                event.target.closest('[data-ingredient-row]').remove();
            }
        });
    </script>
@endpush