<?php

use App\Http\Controllers\BitacoraController;
use Illuminate\Support\Facades\Route;

// Registro de lo que hizo cada usuario: solo lo ve el administrador.
Route::get('bitacora', [BitacoraController::class, 'index'])
    ->middleware('rol:admin')
    ->name('bitacora.index');
