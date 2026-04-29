<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'stock',
    ];

    protected $casts = [
        // Normalizamos el precio para cálculos y respuestas monetarias consistentes.
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Verifica disponibilidad antes de intentar crear/cerrar un pedido
     * y así cortar el flujo temprano cuando el stock es insuficiente.
     */
    public function hasStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }
}
