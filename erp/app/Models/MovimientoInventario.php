<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    /** Tipos de movimiento y el texto que ve el usuario. */
    public const TIPOS = [
        'entrada' => 'Entrada',
        'salida' => 'Salida',
        'ajuste' => 'Ajuste',
        'venta' => 'Venta',
        'devolucion' => 'Devolución',
    ];

    protected $fillable = ['producto_id', 'user_id', 'pedido_id', 'tipo', 'cantidad', 'stock_resultante', 'motivo'];

    protected $casts = [
        'cantidad' => 'integer',
        'stock_resultante' => 'integer',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function nombreTipo(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }
}
