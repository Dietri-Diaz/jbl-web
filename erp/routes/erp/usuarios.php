<?php

use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Administradores con acceso al panel.
Route::resource('usuarios', UsuarioController::class)
    ->except('show')
    ->parameters(['usuarios' => 'usuario']);
