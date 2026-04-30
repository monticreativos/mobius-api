<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Permitimos esta solicitud porque el control real ocurre
     * al validar las credenciales dentro del flujo de autenticación.
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
            // Validamos formato básico para evitar intentos con correos malformados.
            'email' => ['required', 'email'],
            // Pedimos texto plano aquí; la verificación de hash ocurre al autenticar.
            'password' => ['required', 'string'],
        ];
    }
}
