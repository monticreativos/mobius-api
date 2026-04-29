<?php

namespace App\Http\Controllers\Api;

use App\Events\OrderCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(
        path: '/api/orders',
        tags: ['Pedidos'],
        summary: 'Listar pedidos del usuario autenticado',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado de pedidos',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Order'))
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
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

    #[OA\Get(
        path: '/api/orders/{id}',
        tags: ['Pedidos'],
        summary: 'Ver detalle de un pedido',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detalle del pedido',
                content: new OA\JsonContent(ref: '#/components/schemas/Order')
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'Pedido no encontrado'),
        ]
    )]
    public function show(int $id): OrderResource
    {
        $order = Order::query()
            ->with(['user', 'items.product'])
            ->findOrFail($id);

        return new OrderResource($order);
    }

    #[OA\Post(
        path: '/api/orders',
        tags: ['Pedidos'],
        summary: 'Crear un pedido',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['items'],
                properties: [
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        items: new OA\Items(
                            type: 'object',
                            required: ['product_id', 'quantity'],
                            properties: [
                                new OA\Property(property: 'product_id', type: 'integer', example: 1),
                                new OA\Property(property: 'quantity', type: 'integer', example: 2),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Pedido creado correctamente',
                content: new OA\JsonContent(ref: '#/components/schemas/Order')
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(
                response: 422,
                description: 'Error de validación o stock insuficiente'
            ),
        ]
    )]
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

        OrderCreated::dispatch($order->loadMissing('items.product'));

        return new OrderResource($order);
    }

    #[OA\Put(
        path: '/api/orders/{id}/cancel',
        tags: ['Pedidos'],
        summary: 'Cancelar un pedido pendiente',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Pedido cancelado correctamente',
                content: new OA\JsonContent(ref: '#/components/schemas/Order')
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'Pedido no encontrado'),
            new OA\Response(response: 422, description: 'El pedido no puede cancelarse por su estado actual'),
        ]
    )]
    public function cancel(int $id): OrderResource
    {
        $order = Order::query()
            ->with(['user', 'items.product'])
            ->findOrFail($id);

        if ($order->status !== Order::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'status' => 'Solo puedes cancelar pedidos en estado pending. Los pedidos completed o cancelled no permiten cancelación.',
            ]);
        }

        $order->update([
            'status' => Order::STATUS_CANCELLED,
        ]);

        return new OrderResource($order->fresh(['user', 'items.product']));
    }
}
