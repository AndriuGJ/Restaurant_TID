<?php

namespace App\Http\Requests\CashRegisters;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CloseCashRegisterSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('cajas-cerrar');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'closing_amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'closing_amount.required' => 'Debe indicar el monto de cierre.',
        ];
    }
}
