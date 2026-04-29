<?php

namespace App\Providers;

use App\Models\OrderItem;
use App\Observers\OrderItemObserver;
use Illuminate\Http\Resources\Json\JsonResource;
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
        // Los listeners en app/Listeners se registran vía descubrimiento de eventos (withEvents() en bootstrap).
    }
}
