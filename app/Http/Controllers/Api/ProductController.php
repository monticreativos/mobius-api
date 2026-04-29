<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    /**
     * Catálogo público: el cliente arma el carrito con `product_id` al crear el pedido.
     */
    #[OA\Get(
        path: '/api/products',
        tags: ['Productos'],
        summary: 'Listar productos',
        description: 'Devuelve el catálogo con precio y stock actual (sin autenticación).',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado de productos',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Product')
                )
            ),
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        $products = Product::query()
            ->orderBy('id')
            ->get();

        return ProductResource::collection($products);
    }
}
