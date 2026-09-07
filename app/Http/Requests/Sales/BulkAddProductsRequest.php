<?php

namespace App\Http\Requests\Sales;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BulkAddProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pos-ventas');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Selecciona al menos un producto.',
            'items.min' => 'Selecciona al menos un producto.',
            'items.*.product_id.required' => 'Uno de los productos seleccionados no es válido.',
            'items.*.quantity.gt' => 'Las cantidades deben ser mayores a 0.',
        ];
    }
}
