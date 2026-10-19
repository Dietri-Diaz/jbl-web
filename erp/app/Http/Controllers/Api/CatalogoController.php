<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parlante;

class CatalogoController extends Controller
{
    /**
     * GET /api/parlantes
     * Los parlantes publicados, en el orden en que los muestra la landing.
     */
    public function index()
    {
        $parlantes = Parlante::visibles()
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get()
            ->map(fn (Parlante $p) => [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'slug' => $p->slug,
                'tipo' => $p->tipo,
                'potencia_w' => $p->potencia_w,
                'bateria_h' => $p->bateria_h,
                'resistencia' => $p->resistencia,
                'sello' => $p->sello,
                'foto_url' => $p->urlFoto(),
                'foto_alt' => $p->foto_alt,
            ]);

        return response()->json(['data' => $parlantes]);
    }
}
