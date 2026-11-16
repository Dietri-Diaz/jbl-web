<?php

namespace App\Providers;

use App\Models\Mensaje;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Fechas en español: "hace 5 minutos".
        Carbon::setLocale('es');

        // Paginación con el estilo del panel (sin Tailwind).
        Paginator::defaultView('erp.paginacion');

        // Contadores del menú lateral: lo que está esperando atención.
        View::composer('erp.layout', function ($vista) {
            $vista->with('menu', [
                'pedidos' => Pedido::where('estado', 'pendiente')->count(),
                'mensajes' => Mensaje::noLeidos()->count(),
                'stock' => Producto::stockBajo()->count(),
            ]);
        });
    }
}
