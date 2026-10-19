<?php

namespace App\Providers;

use App\Models\Mensaje;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
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

        // El menú lateral muestra cuántos mensajes faltan leer.
        View::composer('erp.layout', function ($vista) {
            $vista->with('mensajesNoLeidos', Schema::hasTable('mensajes') ? Mensaje::noLeidos()->count() : 0);
        });
    }
}
