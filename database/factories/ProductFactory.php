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
            'name' => fake()->randomElement([
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
                'Router WiFi 6',
                'Smartwatch Active',
                'Microfono Condensador',
                'Altavoces Estudio',
                'Hub USB Inteligente',
                'Power Bank 20000mAh',
                'Cargador GaN 100W',
                'Camara Deportiva 4K',
            ]).' '.fake()->unique()->numberBetween(100, 999),
            'price' => fake()->randomFloat(2, 19.99, 2499.99),
            'stock' => fake()->numberBetween(5, 150),
        ];
    }
}
