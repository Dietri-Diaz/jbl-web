<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\Parlante;
use App\Models\User;

class PanelController extends Controller
{
    /** Resumen del ERP: cifras del catálogo, mensajes y administradores. */
    public function inicio()
    {
        return view('erp.inicio', [
            'totalParlantes' => Parlante::count(),
            'publicados' => Parlante::visibles()->count(),
            'noLeidos' => Mensaje::noLeidos()->count(),
            'totalMensajes' => Mensaje::count(),
            'administradores' => User::count(),
            'ultimosMensajes' => Mensaje::latest()->take(5)->get(),
            'catalogo' => Parlante::orderBy('orden')->get(),
        ]);
    }
}
