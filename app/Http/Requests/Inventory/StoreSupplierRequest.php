<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('inventario-ver');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ruc' => ['required', 'string', 'max:20', 'unique:suppliers,ruc'],
            'social_reason' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'status' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.unique' => 'Ya existe un proveedor con ese RUC.',
            'social_reason.required' => 'La razón social es obligatoria.',
        ];
    }
}
