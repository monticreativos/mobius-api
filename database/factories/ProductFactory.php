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
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
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
            'price' => fake()->randomFloat(2, 19.99, 2499.99),
            'stock' => fake()->numberBetween(5, 150),
        ];
    }
}
