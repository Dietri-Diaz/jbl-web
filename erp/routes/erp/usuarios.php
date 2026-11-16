<?php

use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Cuentas con acceso al panel: solo las gestiona el administrador.
Route::resource('usuarios', UsuarioController::class)
    ->except('show')
    ->parameters(['usuarios' => 'usuario'])
    ->middleware('rol:admin');
