<?php

use App\Http\Controllers\Api\CatalogoController;
use Illuminate\Support\Facades\Route;

// Catálogo público que lee la tienda: reglas, categorías y productos.
Route::get('catalogo', [CatalogoController::class, 'index'])->name('api.catalogo');
