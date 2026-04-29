<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
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
            // Eager loading para evitar N+1 al serializar usuario, items y producto.
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
    public function store(StoreOrderRequest $request, OrderService $orderService): JsonResponse
    {
        /** @var User $authenticatedUser */
        $authenticatedUser = Auth::user();
        // Usamos únicamente la carga validada para proteger la creación del pedido.
        $orderItems = $request->validated('items');
        $order = $orderService->createOrderForUser($authenticatedUser, $orderItems);

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
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
            // La cancelación solo aplica a pedidos pendientes para conservar consistencia de inventario y flujo.
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
