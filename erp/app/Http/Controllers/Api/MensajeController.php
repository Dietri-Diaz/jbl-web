<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mensaje;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    /**
     * POST /api/mensajes
     * Recibe el formulario de contacto de la landing.
     */
    public function store(Request $request)
    {
        // Campo trampa: es invisible para las personas. Si llega lleno, lo
        // llenó un bot; se responde como si todo saliera bien y no se guarda.
        if (filled($request->input('sitio_web'))) {
            return response()->json(['mensaje' => 'Mensaje recibido.'], 201);
        }

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:80'],
            'correo' => ['required', 'email', 'max:120'],
            'mensaje' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        Mensaje::create($datos);

        return response()->json(['mensaje' => 'Mensaje recibido.'], 201);
    }
}
