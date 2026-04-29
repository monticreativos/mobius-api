<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Support\Facades\Log;

class NotifyStockUpdate
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $updatedProducts = $event->order->items
            ->map(fn ($orderItem): array => [
                'product_id' => $orderItem->product_id,
                'quantity' => $orderItem->quantity,
                'remaining_stock' => $orderItem->product?->stock,
            ])
            ->all();

        Log::info('Stock actualizado después de crear pedido.', [
            'order_id' => $event->order->id,
            'items' => $updatedProducts,
        ]);
    }
}
