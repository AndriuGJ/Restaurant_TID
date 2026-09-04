<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('productos-gestionar');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:dish,supply,combo'],
            'product_category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'purchase_category_id' => ['nullable', 'integer', 'exists:purchase_categories,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'numeric', 'min:0'],
            'unit_of_measure' => ['nullable', 'string', 'max:50'],
            'image_url' => ['nullable', 'url'],
            'is_pos_item' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'boolean'],
            'ingredients' => ['nullable', 'array'],
            'ingredients.*.ingredient_id' => ['required', 'integer', 'exists:products,id'],
            'ingredients.*.quantity' => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'type.in' => 'El tipo de producto no es válido.',
            'ingredients.*.ingredient_id.required' => 'Seleccione un insumo.',
            'ingredients.*.quantity.gt' => 'La cantidad del insumo debe ser mayor a 0.',
        ];
    }
}
