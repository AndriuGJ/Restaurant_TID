<?php

namespace App\Http\Requests\Configuration;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('configuracion-editar');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('payment_methods', 'name')->ignore($this->route('payment_method')),
            ],
            'type' => ['required', Rule::in(['cash', 'card', 'digital'])],
            'status' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del medio de pago es obligatorio.',
            'name.unique' => 'Ya existe un medio de pago con ese nombre.',
            'type.required' => 'Debe indicar el tipo de medio de pago.',
        ];
    }
}
