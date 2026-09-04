<?php

namespace App\Http\Requests\Sales;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddProductRequest extends FormRequest
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
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Seleccione un producto.',
            'quantity.gt' => 'La cantidad debe ser mayor a 0.',
        ];
    }
}
