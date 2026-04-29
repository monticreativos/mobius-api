<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Attributes as OA;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
#[OA\Schema(
    schema: 'User',
    title: 'User',
    description: 'Modelo base de usuario en la base de datos.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Diego Moreno'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'diego@mobius.dev'),
    ]
)]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $casts = [
        // Permite tratar la verificación como fecha Carbon dentro del dominio.
        'email_verified_at' => 'datetime',
        // Garantiza hashing automático ante asignaciones directas del atributo.
        'password' => 'hashed',
    ];

    /**
     * Pedidos asociados al usuario autenticado.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
