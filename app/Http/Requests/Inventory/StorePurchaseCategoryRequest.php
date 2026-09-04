<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseCategoryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100', 'unique:purchase_categories,name'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Ya existe una categoría de compra con ese nombre.',
        ];
    }
}
