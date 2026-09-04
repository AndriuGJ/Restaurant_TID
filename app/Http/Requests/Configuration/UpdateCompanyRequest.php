<?php

namespace App\Http\Requests\Configuration;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'commercial_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:9','min:9'],
            'commercial_address' => ['nullable', 'string'],
            'ruc' => [
                'required',
                'string',
                'max:11',
                'min:10',
                Rule::unique('companies', 'ruc')->ignore($this->route('company')),
            ],
            'social_reason' => ['required', 'string', 'max:255'],
            'fiscal_address' => ['nullable', 'string'],
            'ubigeo_id' => ['nullable', 'integer', 'exists:ubigeos,id'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'sol_user' => ['nullable', 'string', 'max:50'],
            'sol_password' => ['nullable', 'string', 'max:100'],
            'digital_certificate_path' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la empresa es obligatorio.',
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.max' => 'El RUC solo debe tener 11 caracteres.',
            'phone.max' => 'El numero debe ser igual a 9 caracteres',
            'phone.min' => 'El numero debe ser igual a 9 caracteres',
            'ruc.unique' => 'Ya existe una empresa con ese RUC.',
            'social_reason.required' => 'La razón social es obligatoria.',
            'logo.image' => 'El icono debe ser una imagen (JPG, PNG o WebP).',
            'logo.max' => 'El icono no puede superar los 2 MB.',
        ];
    }
}
