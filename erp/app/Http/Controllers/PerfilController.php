<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    public function editar(Request $request)
    {
        return view('erp.perfil.editar', ['usuario' => $request->user()]);
    }

    public function actualizar(Request $request)
    {
        $usuario = $request->user();

        // Para cambiar la contraseña hay que demostrar que se conoce la actual.
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_actual' => ['nullable', 'required_with:password', 'current_password'],
        ], [
            'password_actual.required_with' => 'Escribe tu contraseña actual para poder cambiarla.',
        ]);

        $usuario->name = $datos['name'];
        $usuario->email = $datos['email'];
        if (! empty($datos['password'])) {
            // El modelo cifra la contraseña al guardarla (cast "hashed").
            $usuario->password = $datos['password'];
        }
        $usuario->save();

        Bitacora::registrar('editar', 'perfil', empty($datos['password'])
            ? 'Actualizó sus datos de perfil'
            : 'Actualizó su perfil y cambió su contraseña');

        return redirect()->route('perfil.editar')->with('ok', 'Se guardaron tus datos.');
    }
}
