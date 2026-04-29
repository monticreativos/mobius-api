<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Listeners\NotifyStockUpdate;
use App\Models\OrderItem;
use App\Observers\OrderItemObserver;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra servicios globales en el contenedor.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializa hooks globales de dominio al arrancar la aplicación.
     */
    public function boot(): void
    {
        // Respuestas JSON planas sin envoltorio `data` (Postman/consumidores esperan arrays u objetos en la raíz).
        JsonResource::withoutWrapping();

        // El Observer se engancha al ciclo de vida de OrderItem:
        // - creating: calcula subtotal antes de persistir.
        // - created: recalcula total del pedido para centralizar la regla.
        OrderItem::observe(OrderItemObserver::class);
        // El listener reacciona al evento de pedido creado para trazabilidad de stock.
        Event::listen(OrderCreated::class, NotifyStockUpdate::class);
    }
}
