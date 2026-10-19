<?php

use App\Http\Controllers\Api\CatalogoController;
use Illuminate\Support\Facades\Route;

// Catálogo público que lee la landing.
Route::get('parlantes', [CatalogoController::class, 'index'])->name('api.parlantes');
