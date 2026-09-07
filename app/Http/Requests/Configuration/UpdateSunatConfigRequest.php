<?php

namespace App\Http\Requests\Configuration;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSunatConfigRequest extends FormRequest
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
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'document_type_id' => ['required', 'integer', Rule::exists('document_types', 'id')->where(fn ($query) => $query->where('type', 'invoice'))],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(['active', 'inactive', 'expired'])],
            'max_receipts' => ['required', 'integer', 'min:1'],
            'used_receipts' => ['required', 'integer', 'min:0'],
            'card_surcharge_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'Debe seleccionar la empresa.',
            'document_type_id.required' => 'Debe seleccionar el tipo de comprobante (boleta o factura).',
            'document_type_id.exists' => 'El tipo de comprobante seleccionado no es válido.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'end_date.required' => 'La fecha de fin es obligatoria.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser posterior o igual al inicio.',
            'max_receipts.required' => 'Debe indicar el tope de comprobantes.',
            'card_surcharge_percentage.max' => 'El recargo por tarjeta no puede superar el 100%.',
        ];
    }
}
