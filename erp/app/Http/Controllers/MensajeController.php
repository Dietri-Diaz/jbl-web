<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim((string) $request->query('q', ''));
        $estado = $request->query('estado', 'todos');

        $mensajes = Mensaje::query()
            ->when($buscar !== '', fn ($c) => $c->where(fn ($c) => $c
                ->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('correo', 'like', "%{$buscar}%")
                ->orWhere('mensaje', 'like', "%{$buscar}%")))
            ->when($estado === 'no-leidos', fn ($c) => $c->where('leido', false))
            ->when($estado === 'leidos', fn ($c) => $c->where('leido', true))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('erp.mensajes.index', compact('mensajes', 'buscar', 'estado'));
    }

    /** Abrir un mensaje lo marca como leído. */
    public function show(Mensaje $mensaje)
    {
        if (! $mensaje->leido) {
            $mensaje->update(['leido' => true]);
        }

        return view('erp.mensajes.ver', compact('mensaje'));
    }

    public function alternarLeido(Mensaje $mensaje)
    {
        $mensaje->update(['leido' => ! $mensaje->leido]);

        return redirect()->route('mensajes.index')->with('ok', $mensaje->leido
            ? "Marcaste como leído el mensaje de {$mensaje->nombre}."
            : "Marcaste como no leído el mensaje de {$mensaje->nombre}.");
    }

    public function destroy(Mensaje $mensaje)
    {
        $mensaje->delete();

        return redirect()->route('mensajes.index')
            ->with('ok', "Se eliminó el mensaje de {$mensaje->nombre}.");
    }
}
