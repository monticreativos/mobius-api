<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Estado base para tests y seed: datos realistas y sin colisiones de nombre.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Nombres fijos + unique() evitan duplicados al sembrar muchas filas en un mismo test.
            'name' => fake()->unique()->randomElement([
                'Laptop Pro',
                'Teclado Mecánico',
                'Mouse Inalámbrico',
                'Monitor UltraWide',
                'Auriculares Bluetooth',
                'Silla Ergonómica',
                'Webcam HD',
                'Docking Station USB-C',
                'Disco SSD 1TB',
                'Tablet Air',
                'Smartphone Plus',
                'Impresora Láser',
            ]),
            // 2 decimales alineados con el cast decimal:2 del modelo.
            'price' => fake()->randomFloat(2, 19.99, 2499.99),
            // Rango acotado para que los escenarios de stock bajo/ok sigan siendo manejables en pruebas.
            'stock' => fake()->numberBetween(5, 150),
        ];
    }
}
