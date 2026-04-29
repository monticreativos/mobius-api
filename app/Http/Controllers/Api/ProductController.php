<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        tags: ['Productos'],
        summary: 'Listar catálogo de productos disponibles',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado público del catálogo',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Product'))
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        // Cacheamos el catálogo porque cambia mucho menos que los pedidos y así mejoramos el tiempo de respuesta.
        $productsPayload = Cache::remember('catalog.products.index', now()->addMinutes(5), function (): array {
            return ProductResource::collection(
                Product::query()
                    ->orderBy('name')
                    ->get()
            )->resolve();
        });

        return response()->json($productsPayload);
    }
}
