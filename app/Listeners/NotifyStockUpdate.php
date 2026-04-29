<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Support\Facades\Log;

class NotifyStockUpdate
{
    /**
     * Registra en logs los productos afectados para trazabilidad
     * tras la creación de un pedido.
     */
    public function handle(OrderCreated $event): void
    {
        // Normalizamos la salida para tener un payload consistente en observabilidad.
        $updatedProducts = $event->order->items
            ->map(fn ($orderItem): array => [
                'product_id' => $orderItem->product_id,
                'quantity' => $orderItem->quantity,
            ])
            ->all();

        Log::info('Stock actualizado después de crear pedido.', [
            'order_id' => $event->order->id,
            'items' => $updatedProducts,
        ]);
    }
}
