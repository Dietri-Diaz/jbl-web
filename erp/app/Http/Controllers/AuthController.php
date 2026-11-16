<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function formulario()
    {
        return view('erp.login');
    }

    public function ingresar(Request $request)
    {
        $datos = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($datos, $request->boolean('recordar'))) {
            return back()
                ->withErrors(['email' => 'El correo o la contraseña no son correctos.'])
                ->onlyInput('email');
        }

        // Una cuenta desactivada no entra aunque la contraseña sea correcta.
        if (! Auth::user()->activo) {
            Auth::logout();

            return back()
                ->withErrors(['email' => 'Tu cuenta está desactivada. Pide acceso a un administrador.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        Auth::user()->update(['ultimo_ingreso' => now()]);
        Bitacora::registrar('ingresar', 'acceso', 'Ingresó al panel');

        return redirect()->intended(route('panel'));
    }

    public function salir(Request $request)
    {
        // Se anota antes de cerrar la sesión para saber quién salió.
        Bitacora::registrar('salir', 'acceso', 'Cerró su sesión');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('ok', 'Cerraste sesión.');
    }
}
