<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTableRequest extends FormRequest
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
            'hall_id' => ['required', 'integer', 'exists:halls,id'],
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tables', 'name')
                    ->where(fn ($query) => $query->where('hall_id', $this->input('hall_id')))
                    ->ignore($this->route('table')),
            ],
            'shape' => ['required', Rule::in(['square', 'round', 'rectangular'])],
            'status' => ['required', Rule::in(['available', 'occupied', 'reserved'])],
        ];
    }

    public function messages(): array
    {
        return [
            'hall_id.required' => 'Debe seleccionar un salón.',
            'hall_id.exists' => 'El salón seleccionado no existe.',
            'name.required' => 'El nombre de la mesa es obligatorio.',
            'name.max' => 'El nombre no puede superar los 50 caracteres.',
            'name.unique' => 'Ya existe una mesa con ese nombre en el salón seleccionado.',
            'shape.required' => 'Debe indicar la forma de la mesa.',
            'status.required' => 'Debe indicar el estado de la mesa.',
        ];
    }
}
