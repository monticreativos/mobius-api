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
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function hasStock(int $quantity): bool
    {
        // Validación de seguridad previa para evitar procesar una orden imposible antes de la transacción.
        return $this->stock >= $quantity;
    }
}
