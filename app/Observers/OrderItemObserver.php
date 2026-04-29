<?php

namespace App\Observers;

use App\Models\OrderItem;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "creating" event.
     */
    public function creating(OrderItem $orderItem): void
    {
        $orderItem->subtotal = (float) $orderItem->quantity * (float) $orderItem->unit_price;
    }

    /**
     * Handle the OrderItem "created" event.
     */
    public function created(OrderItem $orderItem): void
    {
        // Centralizamos el recálculo del total aquí para no duplicar ni olvidar esta regla en otros endpoints.
        $orderItem->order()->update([
            'total' => $orderItem->order->items()->sum('subtotal'),
        ]);
    }
}
