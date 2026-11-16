<?php

use App\Http\Controllers\InventarioController;
use Illuminate\Support\Facades\Route;

// Existencias y su historial. Las rutas fijas van antes de las que llevan {producto}.
Route::get('inventario', [InventarioController::class, 'index'])->name('inventario.index');
Route::get('inventario/movimientos', [InventarioController::class, 'movimientos'])->name('inventario.movimientos');
Route::get('inventario/movimientos/exportar', [InventarioController::class, 'exportar'])->name('inventario.exportar');

// El stock solo cambia desde aquí: cada ajuste deja un movimiento.
Route::get('inventario/{producto}/ajustar', [InventarioController::class, 'ajustar'])->name('inventario.ajustar');
Route::post('inventario/{producto}/ajustar', [InventarioController::class, 'guardar'])->name('inventario.guardar');
