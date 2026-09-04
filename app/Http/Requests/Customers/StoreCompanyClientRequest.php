<?php

namespace App\Http\Requests\Customers;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyClientRequest extends FormRequest
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
            'ruc' => ['required', 'string', 'max:11' , 'min:10', 'unique:company_clients,ruc'],
            'social_reason' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:9', 'min:9'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'document_type_id' => ['nullable', 'integer', 'exists:document_types,id'],
            'status' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.max' => 'El RUC debe tener un maximo de 11 caracteres',
            'ruc.min' => 'El RUC debe tener un minimo de 10 caracteres',
            'ruc.unique' => 'Ya existe una empresa cliente con ese RUC.',
            'phone.max' => 'El numero debe ser igual a 9 caracteres',
            'phone.min' => 'El numero no debe ser menor a 9 caracteres',
            'social_reason.required' => 'La razón social es obligatoria.',
        ];
    }
}
