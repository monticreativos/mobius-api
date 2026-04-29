<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// Rutas públicas de autenticación (token Sanctum tras registro/login).
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Catálogo: necesario para elegir `product_id` al crear un pedido.
Route::get('/products', [ProductController::class, 'index']);

// Pedidos: solo con Bearer token; las rutas con {id} comprueban propiedad con `order.owner`.
Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show'])->middleware('order.owner');
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel'])->middleware('order.owner');
});
