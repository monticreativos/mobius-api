<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = Auth::user();

        $orders = Order::query()
            ->whereBelongsTo($authenticatedUser)
            ->with(['user', 'items.product'])
            ->latest('id')
            ->get();

        return OrderResource::collection($orders);
    }

    public function show(int $id): OrderResource
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = Auth::user();

        $order = Order::query()
            ->whereBelongsTo($authenticatedUser)
            ->with(['user', 'items.product'])
            ->whereKey($id)
            ->first();

        if ($order === null) {
            throw (new ModelNotFoundException())->setModel(Order::class, [$id]);
        }

        return new OrderResource($order);
    }

    public function store(StoreOrderRequest $request): OrderResource
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = Auth::user();
        $orderItems = $request->validated('items');

        // Usamos transacciones para evitar pedidos fantasma sin ítems válidos o con stock insuficiente.
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

        return new OrderResource($order);
    }
}
