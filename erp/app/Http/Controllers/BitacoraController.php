<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $this->texto($request, 'q');
        $usuario = $this->texto($request, 'usuario');
        $modulo = $this->texto($request, 'modulo');
        $accion = $this->texto($request, 'accion');
        $desde = $this->fecha($request, 'desde');
        $hasta = $this->fecha($request, 'hasta');

        $registros = Bitacora::query()
            ->when($buscar !== '', fn ($c) => $c->where('descripcion', 'like', "%{$buscar}%"))
            ->when($usuario !== '', fn ($c) => $c->where('usuario', $usuario))
            ->when($modulo !== '', fn ($c) => $c->where('modulo', $modulo))
            ->when($accion !== '', fn ($c) => $c->where('accion', $accion))
            ->when($desde, fn ($c) => $c->where('created_at', '>=', $desde))
            ->when($hasta, fn ($c) => $c->where('created_at', '<=', $hasta->endOfDay()))
            ->latest()
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('erp.bitacora.index', [
            'registros' => $registros,
            'filtros' => $request->only(['q', 'usuario', 'modulo', 'accion', 'desde', 'hasta']),
            'hayFiltros' => $request->anyFilled(['q', 'usuario', 'modulo', 'accion', 'desde', 'hasta']),

            // Las listas de los filtros salen de lo que ya está registrado.
            'usuarios' => Bitacora::select('usuario')->distinct()->orderBy('usuario')->pluck('usuario'),
            'modulos' => Bitacora::select('modulo')->distinct()->orderBy('modulo')->pluck('modulo'),
            'acciones' => Bitacora::ACCIONES,

            'registrosHoy' => Bitacora::whereDate('created_at', today())->count(),
            'usuariosHoy' => Bitacora::whereDate('created_at', today())->whereNotNull('user_id')->distinct()->count('user_id'),
        ]);
    }
}
