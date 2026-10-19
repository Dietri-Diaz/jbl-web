<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PanelController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/panel');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'formulario'])->name('login');
    Route::post('login', [AuthController::class, 'ingresar'])
        ->middleware('throttle:10,1')
        ->name('login.ingresar');
});

Route::middleware('auth')->prefix('panel')->group(function () {
    Route::get('/', [PanelController::class, 'inicio'])->name('panel');
    Route::post('salir', [AuthController::class, 'salir'])->name('salir');

    // Cada módulo del ERP tiene su propio archivo de rutas en routes/erp/.
    // Así cada integrante agrega el suyo sin tocar este archivo.
    foreach (glob(__DIR__.'/erp/*.php') as $modulo) {
        require $modulo;
    }
});
