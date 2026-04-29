<?php

namespace App\Services;

use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class OrderService
{
    public function createOrderForUser(User $authenticatedUser, array $orderItems): Order
    {
        Log::info('Iniciando creación de pedido.', [
            'user_id' => $authenticatedUser->id,
            'items_count' => count($orderItems),
        ]);

        try {
            // Encapsulamos la transacción para garantizar consistencia y evitar pedidos fantasma en cualquier punto de entrada.
            $order = DB::transaction(function () use ($authenticatedUser, $orderItems): Order {
                $newOrder = Order::query()->create([
                    'user_id' => $authenticatedUser->id,
                    'status' => Order::STATUS_PENDING,
                    'total' => 0,
                ]);

                foreach ($orderItems as $requestedItem) {
                    $product = Product::query()
                        ->lockForUpdate()
                        ->findOrFail((int) $requestedItem['product_id']);

                    $requestedQuantity = (int) $requestedItem['quantity'];

                    if (! $product->hasStock($requestedQuantity)) {
                        throw ValidationException::withMessages([
                            'items' => "Stock insuficiente para el producto {$product->name}.",
                        ]);
                    }

                    Log::info('Stock verificado.', [
                        'user_id' => $authenticatedUser->id,
                        'product_id' => $product->id,
                        'requested_quantity' => $requestedQuantity,
                        'available_stock' => $product->stock,
                    ]);

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

            OrderCreated::dispatch($order->loadMissing('items.product'));

            Log::info("Pedido #{$order->id} creado exitosamente.", [
                'order_id' => $order->id,
                'user_id' => $authenticatedUser->id,
                'total' => (float) $order->total,
            ]);

            return $order;
        } catch (Throwable $exception) {
            Log::error('Error al crear pedido.', [
                'user_id' => $authenticatedUser->id,
                'error' => $exception->getMessage(),
                'exception' => $exception::class,
            ]);

            throw $exception;
        }
    }
}
