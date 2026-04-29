<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('expone el listado público de productos', function (): void {
    Product::factory()->count(2)->create();

    $response = $this->getJson('/api/products');

    $response->assertOk()
        ->assertJsonCount(2)
        ->assertJsonStructure([
            '*' => ['id', 'name', 'price', 'stock'],
        ]);
});
