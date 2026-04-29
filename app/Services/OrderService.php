<?php

namespace App\Services;

use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Crea un pedido para el usuario autenticado y descuenta stock de forma atómica.
     */
    public function createOrderForUser(User $authenticatedUser, array $orderItems): Order
    {
        // Encapsulamos la transacción para garantizar consistencia y evitar pedidos fantasma en cualquier punto de entrada.
        $order = DB::transaction(function () use ($authenticatedUser, $orderItems): Order {
            $newOrder = Order::query()->create([
                'user_id' => $authenticatedUser->id,
                'status' => Order::STATUS_PENDING,
                'total' => 0,
            ]);

            foreach ($orderItems as $requestedItem) {
                $product = Product::query()
                    // Bloqueo pesimista para evitar sobreventa en compras concurrentes.
                    ->lockForUpdate()
                    ->findOrFail((int) $requestedItem['product_id']);

                $requestedQuantity = (int) $requestedItem['quantity'];

                if (! $product->hasStock($requestedQuantity)) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para el producto {$product->name}.",
                    ]);
                }

                OrderItem::query()->create([
                    'order_id' => $newOrder->id,
                    'product_id' => $product->id,
                    'quantity' => $requestedQuantity,
                    'unit_price' => $product->price,
                ]);

                $product->decrement('stock', $requestedQuantity);
            }

            return $newOrder->fresh(['user', 'items.product']);
        });

        // Disparamos efectos secundarios fuera de la transacción una vez confirmado el commit.
        OrderCreated::dispatch($order->loadMissing('items.product'));

        return $order;
    }
}
