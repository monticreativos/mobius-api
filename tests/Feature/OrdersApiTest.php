<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('requiere autenticacion para acceder a pedidos', function (): void {
    $this->getJson('/api/orders')->assertUnauthorized();
    $this->postJson('/api/orders', [])->assertUnauthorized();
});

it('crea pedido, calcula totales y descuenta stock', function (): void {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => 50.00,
        'stock' => 8,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/orders', [
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 3,
            ],
        ],
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('status', Order::STATUS_PENDING)
        ->assertJsonPath('total', 150)
        ->assertJsonPath('items.0.quantity', 3)
        ->assertJsonPath('items.0.subtotal', 150);

    expect($product->fresh()->stock)->toBe(5);
});

it('rechaza creacion de pedido con stock insuficiente', function (): void {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Monitor Pro',
        'stock' => 1,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/orders', [
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 2,
            ],
        ],
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['items']);

    $this->assertDatabaseCount('orders', 0);
    expect($product->fresh()->stock)->toBe(1);
});

it('lista solo pedidos del usuario autenticado', function (): void {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $ownerOrder = Order::query()->create([
        'user_id' => $owner->id,
        'total' => 10,
        'status' => Order::STATUS_PENDING,
    ]);

    Order::query()->create([
        'user_id' => $otherUser->id,
        'total' => 20,
        'status' => Order::STATUS_PENDING,
    ]);

    Sanctum::actingAs($owner);

    $response = $this->getJson('/api/orders');

    $response->assertSuccessful()
        ->assertJsonCount(1)
        ->assertJsonPath('0.id', $ownerOrder->id);
});

it('no permite ver pedido de otro usuario', function (): void {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $order = Order::query()->create([
        'user_id' => $owner->id,
        'total' => 10,
        'status' => Order::STATUS_PENDING,
    ]);

    Sanctum::actingAs($intruder);

    $this->getJson('/api/orders/'.$order->id)
        ->assertNotFound();
});

it('cancela pedido pendiente del propietario', function (): void {
    $user = User::factory()->create();
    $order = Order::query()->create([
        'user_id' => $user->id,
        'total' => 10,
        'status' => Order::STATUS_PENDING,
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson('/api/orders/'.$order->id.'/cancel');

    $response->assertSuccessful()
        ->assertJsonPath('status', Order::STATUS_CANCELLED);
});

it('rechaza cancelar pedido que no esta pendiente', function (): void {
    $user = User::factory()->create();
    $order = Order::query()->create([
        'user_id' => $user->id,
        'total' => 10,
        'status' => Order::STATUS_COMPLETED,
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson('/api/orders/'.$order->id.'/cancel');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});
