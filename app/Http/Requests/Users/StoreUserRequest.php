<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('usuarios-gestionar');
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'dni' => ['nullable', 'string', 'regex:/^\d{8}$/', 'unique:users,dni'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'boolean'],
            'roles' => ['required', 'array'],
            'roles.*' => ['string'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique' => 'Ya existe un usuario con ese correo.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.unique' => 'Ya existe un usuario con ese nombre de usuario.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'dni.regex' => 'El DNI debe tener 8 dígitos.',
            'dni.unique' => 'Ya existe un usuario con ese DNI.',
            'roles.required' => 'Debe asignar al menos un rol.',
        ];
    }
}
