<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use Illuminate\Support\Facades\Log;

class RestoreStockOnCancellation
{
    /**
     * Devuelve unidades al inventario cuando el pedido se marca como cancelado.
     */
    public function handle(OrderCancelled $event): void
    {
        $restoredLines = [];

        foreach ($event->order->items as $orderItem) {
            $product = $orderItem->product;

            if ($product === null) {
                continue;
            }

            $product->increment('stock', $orderItem->quantity);

            $restoredLines[] = [
                'product_id' => $product->id,
                'quantity_restored' => $orderItem->quantity,
                'stock_after' => $product->fresh()->stock,
            ];
        }

        Log::info('Stock restaurado tras cancelación de pedido.', [
            'order_id' => $event->order->id,
            'lines' => $restoredLines,
        ]);
    }
}
