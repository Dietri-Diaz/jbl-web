<?php

use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;

// Pedidos que llegan desde la tienda. "exportar" va antes de {pedido}.
Route::get('pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
Route::get('pedidos/exportar', [PedidoController::class, 'exportar'])->name('pedidos.exportar');
Route::get('pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');
Route::patch('pedidos/{pedido}/estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.estado');
