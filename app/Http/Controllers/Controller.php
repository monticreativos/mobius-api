<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Mobius API de Pedidos",
 *     version="1.0.0",
 *     description="Swagger es nuestra fuente de verdad para que Frontend integre sin depender de preguntas manuales sobre rutas y contratos."
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Token"
 * )
 */
abstract class Controller
{
    //
}
