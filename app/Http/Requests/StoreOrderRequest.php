<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Permitimos crear pedidos a usuarios autenticados; la pertenencia
     * y permisos finos se validan en middleware/policies.
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
            // Forzamos al menos un ítem para evitar órdenes vacías.
            'items' => ['required', 'array', 'min:1'],
            // Confirmamos que cada producto exista antes de procesar stock o totales.
            'items.*.product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            // Evitamos cantidades inválidas que rompan inventario o cálculos.
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
