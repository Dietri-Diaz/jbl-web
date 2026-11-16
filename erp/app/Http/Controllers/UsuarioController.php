<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $this->texto($request, 'q');
        $rol = $this->texto($request, 'rol');
        $estado = $this->texto($request, 'estado');

        $usuarios = User::query()
            ->when($buscar !== '', fn ($c) => $c->where(fn ($c) => $c
                ->where('name', 'like', "%{$buscar}%")
                ->orWhere('email', 'like', "%{$buscar}%")))
            ->when(array_key_exists($rol, config('tienda.roles')), fn ($c) => $c->where('rol', $rol))
            ->when($estado === 'activos', fn ($c) => $c->where('activo', true))
            ->when($estado === 'inactivos', fn ($c) => $c->where('activo', false))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('erp.usuarios.index', [
            'usuarios' => $usuarios,
            'roles' => config('tienda.roles'),
            'filtros' => $request->only(['q', 'rol', 'estado']),
            'hayFiltros' => $request->anyFilled(['q', 'rol', 'estado']),
            'total' => User::count(),
            'activos' => User::where('activo', true)->count(),
            'administradores' => User::where('rol', 'admin')->where('activo', true)->count(),
        ]);
    }

    public function create()
    {
        return view('erp.usuarios.crear', ['usuario' => new User(['rol' => 'editor'])]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rol' => ['required', Rule::in(array_keys(config('tienda.roles')))],
            'activo' => ['nullable', 'boolean'],
        ]);

        // El modelo cifra la contraseña al guardarla (cast "hashed").
        $usuario = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => $datos['password'],
            'rol' => $datos['rol'],
            'activo' => $request->boolean('activo'),
        ]);

        Bitacora::registrar('crear', 'usuarios', "Creó la cuenta de {$usuario->name} ({$usuario->nombreRol()})");

        return redirect()->route('usuarios.index')
            ->with('ok', $usuario->activo
                ? "{$usuario->name} ya puede ingresar al panel."
                : "Se creó la cuenta de {$usuario->name}, pero está desactivada.");
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
            'rol' => ['required', Rule::in(array_keys(config('tienda.roles')))],
            'activo' => ['nullable', 'boolean'],
        ]);

        $activo = $request->boolean('activo');
        $esMiCuenta = $usuario->is($request->user());

        // Nadie se quita a sí mismo el acceso ni el rol de administrador.
        if ($esMiCuenta && ! $activo) {
            return back()->withInput()->withErrors(['activo' => 'No puedes desactivar tu propia cuenta.']);
        }

        if ($esMiCuenta && $datos['rol'] !== 'admin') {
            return back()->withInput()->withErrors(['rol' => 'No puedes quitarte el rol de administrador.']);
        }

        // Si era el último administrador activo, debe seguir siéndolo.
        $dejaDeAdministrar = $datos['rol'] !== 'admin' || ! $activo;

        if ($dejaDeAdministrar && $this->esUltimoAdmin($usuario)) {
            return back()->withInput()->withErrors(['rol' => 'Debe quedar al menos un administrador activo.']);
        }

        $usuario->name = $datos['name'];
        $usuario->email = $datos['email'];
        $usuario->rol = $datos['rol'];
        $usuario->activo = $activo;
        if (! empty($datos['password'])) {
            $usuario->password = $datos['password'];
        }
        $usuario->save();

        Bitacora::registrar('editar', 'usuarios', "Editó la cuenta de {$usuario->name} ({$usuario->nombreRol()}, ".($usuario->activo ? 'activa' : 'desactivada').')');

        return redirect()->route('usuarios.index')
            ->with('ok', "Se guardaron los datos de {$usuario->name}.");
    }

    public function destroy(Request $request, User $usuario)
    {
        if ($usuario->is($request->user())) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        if ($this->esUltimoAdmin($usuario)) {
            return back()->with('error', 'Debe quedar al menos un administrador activo.');
        }

        $usuario->delete();

        Bitacora::registrar('eliminar', 'usuarios', "Eliminó la cuenta de {$usuario->name}");

        return redirect()->route('usuarios.index')
            ->with('ok', "Se quitó el acceso de {$usuario->name}.");
    }

    /** ¿Es un administrador activo y no hay ningún otro? */
    private function esUltimoAdmin(User $usuario): bool
    {
        if (! $usuario->esAdmin() || ! $usuario->activo) {
            return false;
        }

        return ! User::where('rol', 'admin')
            ->where('activo', true)
            ->where('id', '!=', $usuario->id)
            ->exists();
    }
}
