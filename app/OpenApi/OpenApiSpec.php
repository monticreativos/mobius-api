<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Mobius API de Pedidos',
    description: 'Swagger es nuestra fuente de verdad para que Frontend integre sin depender de preguntas manuales sobre rutas y contratos.'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token'
)]
final class OpenApiSpec
{
}
