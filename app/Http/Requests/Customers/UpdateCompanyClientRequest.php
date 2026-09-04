<?php

namespace App\Http\Requests\Customers;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyClientRequest extends FormRequest
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
            'ruc' => [
                'required',
                'string',
                'max:20',
                Rule::unique('company_clients', 'ruc')->ignore($this->route('companyClient')),
            ],
            'social_reason' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'document_type_id' => ['nullable', 'integer', 'exists:document_types,id'],
            'status' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.unique' => 'Ya existe una empresa cliente con ese RUC.',
            'social_reason.required' => 'La razón social es obligatoria.',
        ];
    }
}
