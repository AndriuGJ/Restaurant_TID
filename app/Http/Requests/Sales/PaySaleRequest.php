<?php

namespace App\Http\Requests\Sales;

use App\Models\Configuration\DocumentType;
use App\Models\Configuration\SunatConfig;
use App\Models\Customers\CompanyClient;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PaySaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pos-cobro');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'clientable_type' => ['nullable', 'string', 'in:customer,company'],
            'clientable_id' => ['nullable', 'integer', 'required_with:clientable_type'],
            'document_type_id' => ['nullable', 'integer', 'exists:document_types,id'],
            'guests' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:255'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
            'payments.*.amount' => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'payments.required' => 'Registre al menos un pago.',
            'payments.*.amount.gt' => 'El monto del pago debe ser mayor a 0.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $payout = collect($this->input('payments', []))->sum('amount');
                $total = (float) $this->route('sale')->fresh()->total;

                if ($payout + 0.001 < $total) {
                    $validator->errors()->add(
                        'payments',
                        "El total pagado (S/ {$payout}) es menor al total de la venta (S/ {$total})."
                    );
                }

                $this->validateElectronicDocument($validator);
            },
        ];
    }

    private function validateElectronicDocument(Validator $validator): void
    {
        $documentId = $this->input('document_type_id');

        if (! $documentId) {
            return;
        }

        $documentType = DocumentType::find($documentId);

        if (! $documentType || $documentType->type !== 'invoice') {
            return;
        }

        // Tanto la boleta como la factura exigen su propio bloque SUNAT autorizado
        // (correlativo) vigente y con tope disponible; solo la factura exige además
        // una empresa (RUC). Se distinguen por nomenclature 'B' vs 'F', ambos con type='invoice'.
        if ($this->activeSunatConfig($documentType) === null) {
            $validator->errors()->add(
                'document_type_id',
                'No hay un bloque SUNAT vigente y con tope disponible para emitir el comprobante seleccionado.'
            );
        }

        if (mb_strtoupper($documentType->nomenclature) === 'F') {
            $this->validateFacturaClient($validator);
        }
    }

    private function validateFacturaClient(Validator $validator): void
    {
        if ($this->input('clientable_type') !== 'company') {
            $validator->errors()->add(
                'clientable_type',
                'Para emitir una factura debe seleccionar una empresa (RUC) ya registrada en Clientes.'
            );

            return;
        }

        $company = $this->input('clientable_id') ? CompanyClient::find($this->input('clientable_id')) : null;

        if (! $company) {
            $validator->errors()->add('clientable_id', 'Seleccione la empresa a la que se emite la factura.');
        }
    }

    private function activeSunatConfig(DocumentType $documentType): ?SunatConfig
    {
        return SunatConfig::where('status', 'active')
            ->where('document_type_id', $documentType->id)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->whereColumn('used_receipts', '<', 'max_receipts')
            ->orderBy('id')
            ->first();
    }
}
