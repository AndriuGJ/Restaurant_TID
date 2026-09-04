<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreHallRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100', 'unique:halls,name'],
            'status' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del salón es obligatorio.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'name.unique' => 'Ya existe un salón con ese nombre.',
        ];
    }
}
