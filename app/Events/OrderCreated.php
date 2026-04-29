<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Pedido recién creado que dispara efectos secundarios
     * (por ejemplo, actualización de stock o notificaciones).
     */
    public function __construct(
        /**
         * Entidad del pedido creada y disponible para los listeners.
         */
        public Order $order
    ) {
    }
}
