<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Permitimos registro abierto; las restricciones de acceso
     * se manejan en otras capas si el negocio lo requiere.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Limitamos longitud para proteger consistencia de datos y UI.
            'name' => ['required', 'string', 'max:255'],
            // La unicidad evita cuentas duplicadas con el mismo correo.
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            // Exigimos confirmación y mínimo razonable para elevar seguridad base.
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Necesitamos tu nombre para completar el registro.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.max' => 'El correo electrónico no puede superar los 255 caracteres.',
            'email.unique' => 'Este correo ya está registrado. Intenta iniciar sesión o usa otro correo.',
            'password.required' => 'La contraseña es obligatoria para crear tu cuenta.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
