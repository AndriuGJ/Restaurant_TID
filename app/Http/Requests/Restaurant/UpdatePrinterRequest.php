<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePrinterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:60'],
            'connection_type' => ['sometimes', 'string', 'in:network,local'],
            'ip_address' => ['required_if:connection_type,network', 'nullable', 'ip'],
            'port' => ['required_if:connection_type,network', 'nullable', 'integer', 'min:1', 'max:65535'],
            'queue_name' => ['nullable', 'string', 'max:60'],
            'send_raw' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la impresora es obligatorio.',
            'name.max' => 'El nombre no puede superar los 60 caracteres.',
            'ip_address.required_if' => 'La dirección IP es obligatoria para una impresora de red.',
            'ip_address.ip' => 'La dirección IP no es válida.',
            'port.required_if' => 'El puerto es obligatorio para una impresora de red.',
            'port.integer' => 'El puerto debe ser un número.',
        ];
    }
}
