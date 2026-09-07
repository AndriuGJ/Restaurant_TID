<div class="space-y-5">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input id="name" name="name" type="text" value="{{ old('name', $product->name ?? '') }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="type" class="block text-sm font-medium text-gray-700">Tipo</label>
            <select id="type" name="type" data-type-target="ingredients-section"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                @php $currentType = old('type', $product->type ?? 'dish'); @endphp
                <option value="dish" @selected($currentType === 'dish')>Plato</option>
                <option value="supply" @selected($currentType === 'supply')>Insumo</option>
                <option value="combo" @selected($currentType === 'combo')>Combo</option>
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
        <textarea id="description" name="description" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="product_category_id" class="block text-sm font-medium text-gray-700">Categoría de producto</label>
            <select id="product_category_id" name="product_category_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Sin categoría</option>
                @foreach ($productCategories as $category)
                    <option value="{{ $category->id }}" @selected(old('product_category_id', $product->product_category_id ?? '') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="purchase_category_id" class="block text-sm font-medium text-gray-700">Categoría de compra</label>
            <select id="purchase_category_id" name="purchase_category_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Sin categoría</option>
                @foreach ($purchaseCategories as $category)
                    <option value="{{ $category->id }}" @selected(old('purchase_category_id', $product->purchase_category_id ?? '') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label for="sale_price" class="block text-sm font-medium text-gray-700">Precio de venta (S/)</label>
            <input id="sale_price" name="sale_price" type="number" step="0.01" min="0" value="{{ old('sale_price', $product->sale_price ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label for="cost_price" class="block text-sm font-medium text-gray-700">Costo (S/)</label>
            <input id="cost_price" name="cost_price" type="number" step="0.01" min="0" value="{{ old('cost_price', $product->cost_price ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label for="price" class="block text-sm font-medium text-gray-700">Precio POS (S/)</label>
            <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
            <input id="stock" name="stock" type="number" step="0.01" min="0" value="{{ old('stock', $product->stock ?? 0) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label for="unit_of_measure" class="block text-sm font-medium text-gray-700">Unidad de medida</label>
            <input id="unit_of_measure" name="unit_of_measure" type="text" value="{{ old('unit_of_measure', $product->unit_of_measure ?? 'unidad') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>
    </div>

    {{-- Imagen del producto --}}
    <div class="rounded-md border border-dashed border-gray-300 bg-gray-50 p-5">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Imagen del producto</h2>
        <p class="mt-1 text-xs text-gray-500">Se mostrará en el POS y en el listado de productos. JPG, PNG o WebP · máx. 2 MB.</p>

        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-md border border-gray-200 bg-white">
                @if (!empty($product->image_url))
                    <img id="image-preview" src="{{ asset('storage/'.$product->image_url) }}" alt="{{ $product->name }}"
                        class="h-full w-full object-cover p-0">
                @else
                    <img id="image-preview" src="" alt="" class="hidden h-full w-full object-cover p-0">
                    <svg id="image-placeholder" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h15a1.5 1.5 0 011.5 1.5v7.5a1.5 1.5 0 01-1.5 1.5H6.75a1.5 1.5 0 01-1.5 1.5v4.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 18.75 7.5 9l3 4.5 1.5-2.25L16.5 15.75"/>
                    </svg>
                @endif
            </div>
            <div class="flex-1">
                <label for="image_url" class="inline-flex cursor-pointer items-center gap-2 rounded-md border border-brand-300 bg-white px-4 py-2 text-sm font-semibold text-brand-600 shadow-sm hover:bg-brand-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Subir imagen
                </label>
                <input id="image_url" name="image_url" type="file" accept="image/jpeg,image/png,image/webp" class="hidden">
                <p class="mt-2 text-xs text-gray-500">Si no subes una imagen, se mostrará un placeholder.</p>
                @error('image_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div id="ingredients-section">
        <div class="flex items-center justify-between">
            <label class="block text-sm font-medium text-gray-700">Ingredientes</label>
            <button type="button" data-add-ingredient
                class="text-sm font-medium text-brand-600 hover:text-brand-500">+ Agregar insumo</button>
        </div>
        <div data-ingredients-list class="mt-2 space-y-2">
            @php $ingredientRows = old('ingredients', $product->ingredients ?? []); @endphp
            @forelse ($ingredientRows as $row)
                <div data-ingredient-row class="flex items-center gap-2">
                    <select name="ingredients[{{ $loop->index }}][ingredient_id]"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">Seleccione insumo</option>
                        @foreach ($supplies as $supply)
                            <option value="{{ $supply->id }}" @selected($row['ingredient_id'] == $supply->id)>{{ $supply->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" step="0.001" min="0" name="ingredients[{{ $loop->index }}][quantity]" placeholder="Cantidad"
                        value="{{ $row['quantity'] ?? '' }}"
                        class="w-28 rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    <button type="button" data-remove-ingredient
                        class="text-sm font-medium text-red-600 hover:text-red-500">Quitar</button>
                </div>
            @empty
                <p data-ingredients-empty class="text-sm text-gray-500">Este plato no tiene ingredientes definidos.</p>
            @endforelse
        </div>
        @error('ingredients')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-6">
        <label for="is_pos_item" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
            <input id="is_pos_item" name="is_pos_item" type="checkbox" value="1"
                {{ old('is_pos_item', $product->is_pos_item ?? true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
            Visible en POS
        </label>

        <label for="status" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
            <input id="status" name="status" type="checkbox" value="1"
                {{ old('status', $product->status ?? true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
            Activo
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
            Guardar
        </button>
        <a href="{{ route('inventory.products.index') }}"
            class="text-sm font-medium text-gray-600 hover:text-gray-500">
            Cancelar
        </a>
    </div>
</div>

@push('scripts')
    <script>
        document.getElementById('image_url').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (ev) {
                const preview = document.getElementById('image-preview');
                const placeholder = document.getElementById('image-placeholder');
                preview.src = ev.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        });

        const ingredientOption = (index) => `
            <select name="ingredients[${index}][ingredient_id]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Seleccione insumo</option>
                @foreach ($supplies as $supply)
                    <option value="{{ $supply->id }}">{{ $supply->name }}</option>
                @endforeach
            </select>
            <input type="number" step="0.001" min="0" name="ingredients[${index}][quantity]" placeholder="Cantidad" class="w-28 rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <button type="button" data-remove-ingredient class="text-sm font-medium text-red-600 hover:text-red-500">Quitar</button>
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
