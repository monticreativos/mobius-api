<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Product',
    title: 'Product',
    description: 'Representación pública de un producto disponible en catálogo.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Laptop Pro'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 1499.99),
        new OA\Property(property: 'stock', type: 'integer', example: 23),
    ]
)]
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'stock' => $this->stock,
        ];
    }
}
