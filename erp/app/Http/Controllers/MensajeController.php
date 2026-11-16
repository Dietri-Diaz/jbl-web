<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Mensaje;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $this->texto($request, 'q');
        $estado = $this->texto($request, 'estado', 'todos');

        // La búsqueda se reutiliza para la tabla y para el conteo de cada pestaña.
        $buscados = fn () => Mensaje::query()
            ->when($buscar !== '', fn ($c) => $c->where(fn ($c) => $c
                ->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('correo', 'like', "%{$buscar}%")
                ->orWhere('mensaje', 'like', "%{$buscar}%")));

        $mensajes = $buscados()
            ->when($estado === 'no-leidos', fn ($c) => $c->where('leido', false))
            ->when($estado === 'leidos', fn ($c) => $c->where('leido', true))
            ->latest()
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('erp.mensajes.index', [
            'mensajes' => $mensajes,
            'buscar' => $buscar,
            'estado' => in_array($estado, ['no-leidos', 'leidos'], true) ? $estado : 'todos',
            'conteos' => [
                'todos' => $buscados()->count(),
                'no-leidos' => $buscados()->where('leido', false)->count(),
                'leidos' => $buscados()->where('leido', true)->count(),
            ],
        ]);
    }

    /** Abrir un mensaje lo marca como leído. */
    public function show(Mensaje $mensaje)
    {
        // Abrirlo lo marca como leído, salvo para el rol de solo lectura,
        // que puede mirar pero no cambiar nada.
        if (! $mensaje->leido && auth()->user()->puedeEditar()) {
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

        Bitacora::registrar('eliminar', 'mensajes', "Eliminó el mensaje de {$mensaje->nombre} ({$mensaje->correo})");

        return redirect()->route('mensajes.index')
            ->with('ok', "Se eliminó el mensaje de {$mensaje->nombre}.");
    }
}
