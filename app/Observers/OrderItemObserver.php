<?php

namespace App\Observers;

use App\Models\OrderItem;

class OrderItemObserver
{
    /**
     * Antes de persistir el ítem calculamos el subtotal como snapshot monetario.
     */
    public function creating(OrderItem $orderItem): void
    {
        // Evita depender del cliente para un valor derivado crítico.
        $orderItem->subtotal = (float) $orderItem->quantity * (float) $orderItem->unit_price;
    }

    /**
     * Tras crear el ítem, recalculamos el total del pedido asociado.
     */
    public function created(OrderItem $orderItem): void
    {
        // Centralizamos el recálculo del total aquí para no duplicar ni olvidar esta regla en otros endpoints.
        $orderItem->order()->update([
            'total' => $orderItem->order->items()->sum('subtotal'),
        ]);
    }
}
