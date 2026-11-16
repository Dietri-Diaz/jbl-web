<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Permisos del panel según el rol del usuario.
 *
 *   ->middleware('rol')        cualquier usuario activo puede ver; solo
 *                              administradores y editores pueden modificar.
 *   ->middleware('rol:admin')  módulo exclusivo del administrador.
 */
class VerificarRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        // Una cuenta desactivada pierde el acceso aunque tenga la sesión abierta.
        if (! $usuario->activo) {
            Auth::logout();
            $request->session()->invalidate();

            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta está desactivada. Pide acceso a un administrador.']);
        }

        if ($roles && ! in_array($usuario->rol, $roles, true)) {
            return redirect()->route('panel')
                ->with('error', 'Tu rol no tiene acceso a ese módulo.');
        }

        // El rol "solo lectura" puede mirar, salir y editar su propio perfil.
        $soloLee = ! $usuario->puedeEditar();
        $modifica = ! $request->isMethod('GET') && ! $request->isMethod('HEAD');
        $permitido = $request->routeIs('salir', 'perfil.*');

        if ($soloLee && $modifica && ! $permitido) {
            return back()->with('error', 'Tu rol es de solo lectura: no puedes hacer cambios.');
        }

        return $next($request);
    }
}
