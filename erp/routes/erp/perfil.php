<?php

use App\Http\Controllers\PerfilController;
use Illuminate\Support\Facades\Route;

// Cada usuario edita sus propios datos (también el rol de solo lectura).
Route::get('perfil', [PerfilController::class, 'editar'])->name('perfil.editar');
Route::put('perfil', [PerfilController::class, 'actualizar'])->name('perfil.actualizar');
