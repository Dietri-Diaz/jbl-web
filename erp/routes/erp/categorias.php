<?php

use App\Http\Controllers\CategoriaController;
use Illuminate\Support\Facades\Route;

// Categorías en las que se agrupan los productos.
Route::resource('categorias', CategoriaController::class)
    ->except('show')
    ->parameters(['categorias' => 'categoria']);
