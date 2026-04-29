<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    // WithoutModelEvents: el seed no dispara observers ni eventos de Eloquent (evita side effects en datos de prueba).
    use WithoutModelEvents;

    /**
     * Datos mínimos reproducibles para entorno local: credenciales fijas y catálogo de ejemplo.
     */
    public function run(): void
    {
        // Idempotente: re-ejecutar el seeder no duplica el usuario demo.
        User::query()->updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Administrador Demo',
            // Hash explícito para coincidir con la contraseña documentada en desarrollo.
            'password' => Hash::make('password'),
        ]);

        // Variedad de productos vía factory para probar listados y flujos de pedido.
        Product::factory(10)->create();
    }
}
