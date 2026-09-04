<?php

namespace App\Http\Requests\Customers;

use App\Models\Configuration\DocumentType as ConfigurationDocumentType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('clientes-gestionar');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //Obtenemos el limite del Tipo de Documento de la tabla Documentos
        $documentType = ConfigurationDocumentType::find($this->document_type_id);
        $maxLimit = $documentType?->character_limit ?? 20;
        return [
            'dni',
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:9'],
            'email' => ['nullable', 'email', 'max:150'],
            'reference_address' => ['nullable', 'string'],
            'document_type_id' => ['nullable', 'integer', 'exists:document_types,id'],
            'document_number' => ['nullable', 'string', "max:{$maxLimit}"],
            'status' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del cliente es obligatorio.',
        ];
    }
}
