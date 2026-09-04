<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShiftRequest extends FormRequest
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
                Rule::unique('shifts', 'name')->ignore($this->route('shift')),
            ],
            'status' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del turno es obligatorio.',
            'name.max' => 'El nombre no puede superar los 50 caracteres.',
            'name.unique' => 'Ya existe un turno con ese nombre.',
        ];
    }
}
