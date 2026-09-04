<?php

namespace App\Http\Requests\Configuration;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentTypeRequest extends FormRequest
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
                Rule::unique('document_types', 'name')->ignore($this->route('document_type')),
            ],
            'nomenclature' => ['nullable', 'string', 'max:20'],
            'character_limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'type' => ['required', Rule::in(['identification', 'invoice'])],
            'status' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del documento es obligatorio.',
            'name.unique' => 'Ya existe un documento con ese nombre.',
            'type.required' => 'Debe indicar el tipo de documento.',
        ];
    }
}
