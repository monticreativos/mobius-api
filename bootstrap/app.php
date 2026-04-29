<?php

use App\Http\Middleware\CheckOrderOwner;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php', // Mantenemos web para que los tests no fallen
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'order.owner' => CheckOrderOwner::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Respuestas JSON seguras para la API: nunca exponemos traza, rutas de archivo ni detalle interno de Laravel.
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            // Mantener el formato habitual de validación (422).
            if ($e instanceof ValidationException) {
                return null;
            }

            // Sanctum / auth ya devuelven JSON breve.
            if ($e instanceof AuthenticationException) {
                return null;
            }

            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                $message = match (true) {
                    $status === 404 => 'No encontrado.',
                    $status === 403 => 'No autorizado.',
                    $status === 429 => 'Demasiadas peticiones.',
                    $status >= 500 => 'Error interno del servidor.',
                    default => 'Solicitud incorrecta.',
                };

                return response()->json(['message' => $message], $status);
            }

            return response()->json([
                'message' => 'Error interno del servidor.',
            ], 500);
        });
    })->create();
