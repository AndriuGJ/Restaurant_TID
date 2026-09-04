<?php

namespace App\Http\Requests\CashRegisters;

use App\Models\Restaurant\CashRegisterSession;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class OpenCashRegisterSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('cajas-abrir');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cash_register_id' => ['required', 'integer', 'exists:cash_registers,id'],
            'shift_id' => ['required', 'integer', 'exists:shifts,id'],
            'opening_amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $cashRegisterId = $this->input('cash_register_id');

            if (! $cashRegisterId) {
                return;
            }

            $open = CashRegisterSession::where('cash_register_id', $cashRegisterId)
                ->where('status', 'open')
                ->exists();

            if ($open) {
                $validator->errors()->add('cash_register_id', 'Esta caja ya tiene una sesión abierta.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'cash_register_id.required' => 'Debe seleccionar la caja.',
            'shift_id.required' => 'Debe seleccionar el turno.',
            'opening_amount.required' => 'Debe indicar el monto de apertura.',
        ];
    }
}
