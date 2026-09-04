<?php

namespace App\Http\Requests\Sales;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MoveTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pos-ver');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pos_x' => ['required', 'integer', 'min:0'],
            'pos_y' => ['required', 'integer', 'min:0'],
        ];
    }
}
