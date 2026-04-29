<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('registra un usuario y devuelve token', function (): void {
    $response = $this->postJson('/api/register', [
        'name' => 'Diego Moreno',
        'email' => 'diego@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'token',
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'diego@example.com',
    ]);
});

it('valida que no se repita el email en registro', function (): void {
    User::factory()->create([
        'email' => 'diego@example.com',
    ]);

    $response = $this->postJson('/api/register', [
        'name' => 'Diego Moreno',
        'email' => 'diego@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('inicia sesion y devuelve token', function (): void {
    User::factory()->create([
        'email' => 'diego@example.com',
        'password' => Hash::make('secret123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'diego@example.com',
        'password' => 'secret123',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'token',
        ]);
});

it('rechaza credenciales invalidas en login', function (): void {
    User::factory()->create([
        'email' => 'diego@example.com',
        'password' => Hash::make('secret123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'diego@example.com',
        'password' => 'otra-clave',
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Las credenciales ingresadas no son válidas.');
});
