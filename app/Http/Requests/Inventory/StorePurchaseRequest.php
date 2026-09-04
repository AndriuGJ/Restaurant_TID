<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('compras-gestionar');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'document_type_id' => ['required', 'integer', Rule::exists('document_types', 'id')->where('type', 'invoice')],
            'purchase_type' => ['required', 'in:contado,credito'],
            'series' => ['nullable', 'string', 'max:20'],
            'number' => ['required', 'string', 'max:50'],
            'purchase_date' => ['required', 'date'],
            'details' => ['required', 'array', 'min:1'],
            'details.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'details.*.quantity' => ['required', 'numeric', 'gt:0'],
            'details.*.unit_price' => ['required', 'numeric', 'gte:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Seleccione un proveedor.',
            'document_type_id.required' => 'Seleccione el tipo de comprobante.',
            'number.required' => 'El número de comprobante es obligatorio.',
            'details.required' => 'Agregue al menos un producto.',
            'details.*.product_id.required' => 'Seleccione un producto.',
            'details.*.quantity.gt' => 'La cantidad debe ser mayor a 0.',
        ];
    }
}
