<?php

use App\Http\Controllers\Api\MensajeController;
use Illuminate\Support\Facades\Route;

// Formulario de contacto de la landing: máximo 5 envíos por minuto por visitante.
Route::post('mensajes', [MensajeController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('api.mensajes');
