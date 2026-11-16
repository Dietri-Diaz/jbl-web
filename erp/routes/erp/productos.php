<?php

use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

// Catálogo de productos que se vende en la tienda.
// "exportar" va antes del resource para que no se confunda con {producto}.
Route::get('productos/exportar', [ProductoController::class, 'exportar'])->name('productos.exportar');

Route::resource('productos', ProductoController::class)
    ->except('show')
    ->parameters(['productos' => 'producto']);

Route::patch('productos/{producto}/visible', [ProductoController::class, 'alternarVisible'])
    ->name('productos.visible');
