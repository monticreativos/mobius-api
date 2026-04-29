<?php

use App\Http\Middleware\CheckOrderOwner;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $exceptions->shouldRenderJsonWhen(function (Request $request, \Throwable $exception): bool {
            return $request->is('api/*') || $request->expectsJson() || $request->wantsJson();
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No autenticado.',
            ], 401);
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Recurso no encontrado.',
            ], 404);
        });

        $exceptions->render(function (ValidationException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage() !== '' ? $exception->getMessage() : 'Error de validación.',
            ], 422);
        });

        $exceptions->render(function (\Throwable $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $statusCode = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : 500;

            $message = $statusCode >= 500
                ? 'Error interno del servidor'
                : ($exception->getMessage() !== '' ? $exception->getMessage() : 'Error en la solicitud.');

            return response()->json([
                'status' => 'error',
                'message' => $message,
            ], $statusCode);
        });
    })->create();
