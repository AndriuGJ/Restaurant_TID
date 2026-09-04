<?php

namespace App\Http\Requests\Customers;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('clientes-gestionar');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'reference_address' => ['nullable', 'string'],
            'document_type_id' => ['nullable', 'integer', 'exists:document_types,id'],
            'document_number' => ['nullable', 'string', 'max:50'],
            'status' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del cliente es obligatorio.',
        ];
    }
}
