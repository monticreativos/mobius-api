<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckOrderOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Usamos route('id') para leer el parámetro de forma limpia y proteger la privacidad de pedidos que no pertenecen al cliente autenticado.
        $orderId = (int) $request->route('id');

        $ownsOrder = Order::query()
            ->whereKey($orderId)
            ->where('user_id', Auth::id())
            ->exists();

        if (! $ownsOrder) {
            throw (new ModelNotFoundException())->setModel(Order::class, [$orderId]);
        }

        return $next($request);
    }
}
