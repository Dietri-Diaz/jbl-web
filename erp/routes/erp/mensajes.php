<?php

use App\Http\Controllers\MensajeController;
use Illuminate\Support\Facades\Route;

// Bandeja de mensajes que llegan desde el formulario de contacto.
Route::get('mensajes', [MensajeController::class, 'index'])->name('mensajes.index');
Route::get('mensajes/{mensaje}', [MensajeController::class, 'show'])->name('mensajes.show');
Route::patch('mensajes/{mensaje}/leido', [MensajeController::class, 'alternarLeido'])->name('mensajes.leido');
Route::delete('mensajes/{mensaje}', [MensajeController::class, 'destroy'])->name('mensajes.destroy');
