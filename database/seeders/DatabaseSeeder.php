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
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $mainClient = User::query()->updateOrCreate([
            'email' => 'cliente@example.com',
        ], [
            'name' => 'Cliente Principal',
            'password' => Hash::make('password'),
        ]);

        $randomUsers = User::factory(4)->create();
        $allUsers = $randomUsers->prepend($mainClient);

        Product::factory(5)->state(fn (): array => ['stock' => 0])->create();
        Product::factory(5)->state(fn (): array => ['stock' => 1])->create();
        Product::factory(20)->state(fn (): array => ['stock' => fake()->numberBetween(15, 180)])->create();

        // Generamos pedidos con estados variados para probar middleware de ownership y regla de cancelación en escenarios reales.
        foreach ($allUsers as $activeUser) {
            $ordersCount = fake()->numberBetween(3, 6);
            $availableStatuses = collect([
                Order::STATUS_PENDING,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ])->shuffle();

            for ($orderIndex = 0; $orderIndex < $ordersCount; $orderIndex++) {
                $newOrder = Order::query()->create([
                    'user_id' => $activeUser->id,
                    'status' => $availableStatuses[$orderIndex % $availableStatuses->count()],
                    'total' => 0,
                ]);
                $orderTotal = 0.0;

                $itemsCount = fake()->numberBetween(1, 3);
                $selectedProducts = Product::query()
                    ->where('stock', '>', 0)
                    ->inRandomOrder()
                    ->limit($itemsCount)
                    ->get();

                if ($selectedProducts->isEmpty()) {
                    continue;
                }

                foreach ($selectedProducts as $selectedProduct) {
                    if ($selectedProduct->stock <= 0) {
                        continue;
                    }

                    $requestedQuantity = min(fake()->numberBetween(1, 3), $selectedProduct->stock);
                    $unitPrice = (float) $selectedProduct->price;
                    $subtotal = $requestedQuantity * $unitPrice;

                    OrderItem::query()->create([
                        'order_id' => $newOrder->id,
                        'product_id' => $selectedProduct->id,
                        'quantity' => $requestedQuantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ]);

                    $selectedProduct->decrement('stock', $requestedQuantity);
                    $orderTotal += $subtotal;
                }

                $newOrder->update([
                    'total' => $orderTotal,
                ]);
            }
        }
    }
}
