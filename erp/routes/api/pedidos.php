<?php

use App\Http\Controllers\Api\PedidoController;
use Illuminate\Support\Facades\Route;

// Pedidos de la tienda: máximo 10 por minuto por visitante.
Route::post('pedidos', [PedidoController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('api.pedidos');

// Seguimiento de un pedido con su código y el correo del cliente.
Route::get('pedidos/{codigo}', [PedidoController::class, 'show'])
    ->middleware('throttle:30,1')
    ->name('api.pedidos.ver');
