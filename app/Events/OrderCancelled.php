<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCancelled implements ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Pedido pasado a cancelado; permite efectos secundarios como devolver inventario.
     * Se despacha tras commit para que el inventario refleje el estado persistido del pedido.
     */
    public function __construct(
        /**
         * Pedido ya persistido con estado `cancelled` y líneas cargadas para los listeners.
         */
        public Order $order
    ) {
    }
}
