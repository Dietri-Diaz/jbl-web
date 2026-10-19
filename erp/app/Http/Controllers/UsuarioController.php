<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        return view('erp.usuarios.index', [
            'usuarios' => User::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('erp.usuarios.crear', ['usuario' => new User()]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $usuario = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
        ]);

        return redirect()->route('usuarios.index')
            ->with('ok', "{$usuario->name} ya puede ingresar al panel.");
    }

    public function edit(User $usuario)
    {
        return view('erp.usuarios.editar', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        // La contraseña solo cambia si se escribe una nueva.
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $usuario->name = $datos['name'];
        $usuario->email = $datos['email'];
        if (! empty($datos['password'])) {
            $usuario->password = Hash::make($datos['password']);
        }
        $usuario->save();

        return redirect()->route('usuarios.index')
            ->with('ok', "Se guardaron los datos de {$usuario->name}.");
    }

    public function destroy(Request $request, User $usuario)
    {
        if ($usuario->is($request->user())) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta mientras la usas.');
        }

        if (User::count() === 1) {
            return back()->with('error', 'Debe quedar al menos un administrador.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('ok', "Se quitó el acceso de {$usuario->name}.");
    }
}
