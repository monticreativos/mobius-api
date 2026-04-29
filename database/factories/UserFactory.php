<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Hash de contraseña reutilizado entre instancias para no repetir `Hash::make` en tests masivos.
     */
    protected static ?string $password;

    /**
     * Estado base para tests y seed: usuario verificado y credenciales conocidas.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            // Emails únicos evitan violaciones de índice al crear muchos usuarios seguidos.
            'email' => fake()->unique()->safeEmail(),
            // Por defecto verificado para simplificar flujos que exigen email confirmado.
            'email_verified_at' => now(),
            // Alineado con el cast `hashed` del modelo; cacheamos el hash por rendimiento en datasets grandes.
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Usuario con email no verificado (registro, middleware `verified`, etc.).
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
