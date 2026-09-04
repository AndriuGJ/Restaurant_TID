<?php

namespace App\Http\Requests\Sales;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DeliveryInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pos-delivery');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'delivery_provider_id' => ['nullable', 'integer', 'exists:delivery_providers,id'],
            'delivery_person_name' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'delivery_provider_id.exists' => 'El proveedor de delivery seleccionado no existe.',
        ];
    }
}
