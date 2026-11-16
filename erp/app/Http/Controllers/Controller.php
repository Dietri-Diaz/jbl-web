<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

abstract class Controller
{
    /**
     * Lee un filtro de texto de la dirección (?q=flip). Si llega algo que no
     * es texto, como ?q[]=x, se toma como vacío en vez de provocar un error.
     */
    protected function texto(Request $request, string $campo, string $porDefecto = ''): string
    {
        $valor = $request->query($campo, $porDefecto);

        return is_string($valor) ? trim($valor) : $porDefecto;
    }

    /**
     * Lee una fecha de los filtros (?desde=2026-11-01). Si viene vacía o mal
     * escrita devuelve null, así el filtro simplemente no se aplica.
     */
    protected function fecha(Request $request, string $campo): ?Carbon
    {
        $texto = $this->texto($request, $campo);

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $texto)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $texto)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
