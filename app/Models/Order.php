<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Order',
    title: 'Order',
    description: 'Representa un pedido del usuario autenticado.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 10),
        new OA\Property(property: 'status', type: 'string', example: 'pending'),
        new OA\Property(property: 'total', type: 'number', format: 'float', example: 149.9),
        new OA\Property(property: 'user', ref: '#/components/schemas/UserResource'),
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/OrderItem')
        ),
    ]
)]
class Order extends Model
{
    use HasFactory;

    /**
     * Estado inicial al crear el pedido, previo a confirmación/finalización.
     */
    public const STATUS_PENDING = 'pending';

    /**
     * Estado final cuando el pedido fue procesado correctamente.
     */
    public const STATUS_COMPLETED = 'completed';

    /**
     * Estado final cuando el pedido fue anulado por usuario o sistema.
     */
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'total',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
        // Guardamos y serializamos el total con 2 decimales para consistencia monetaria.
        'total' => 'decimal:2',
    ];

    /**
     * Usuario propietario del pedido.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ítems que componen el pedido y su desglose de productos.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
