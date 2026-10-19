<?php

use App\Http\Controllers\ParlanteController;
use Illuminate\Support\Facades\Route;

// Módulo de parlantes: el catálogo que muestra la landing.
Route::resource('parlantes', ParlanteController::class)
    ->except('show')
    ->parameters(['parlantes' => 'parlante']);

Route::patch('parlantes/{parlante}/visible', [ParlanteController::class, 'alternarVisible'])
    ->name('parlantes.visible');
