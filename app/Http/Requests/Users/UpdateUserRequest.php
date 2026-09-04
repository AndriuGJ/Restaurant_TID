<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('usuarios-gestionar');
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'dni' => ['nullable', 'string', 'regex:/^\d{8}$/', Rule::unique('users', 'dni')->ignore($user->id)],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
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
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'dni.regex' => 'El DNI debe tener 8 dígitos.',
            'dni.unique' => 'Ya existe un usuario con ese DNI.',
            'roles.required' => 'Debe asignar al menos un rol.',
        ];
    }
}
