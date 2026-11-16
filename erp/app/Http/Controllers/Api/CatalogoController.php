<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Soporte\CatalogoTienda;

class CatalogoController extends Controller
{
    /**
     * GET /api/catalogo
     * Todo lo que la tienda necesita en una sola petición: reglas de envío,
     * categorías y productos publicados.
     */
    public function index()
    {
        return response()->json(CatalogoTienda::datos());
    }
}
