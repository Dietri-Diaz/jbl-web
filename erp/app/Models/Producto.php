<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'categoria_id', 'nombre', 'slug', 'sku', 'tipo', 'descripcion', 'color',
        'precio', 'precio_oferta', 'stock', 'stock_minimo',
        'potencia_w', 'bateria_h', 'resistencia',
        'foto', 'foto_alt', 'sello', 'destacado', 'visible', 'orden',
    ];

    protected $casts = [
        'precio' => 'float',
        'precio_oferta' => 'float',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'potencia_w' => 'integer',
        'bateria_h' => 'integer',
        'destacado' => 'boolean',
        'visible' => 'boolean',
        'orden' => 'integer',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    /** El movimiento más reciente, para mostrarlo en las existencias. */
    public function ultimoMovimiento(): HasOne
    {
        return $this->hasOne(MovimientoInventario::class)->latestOfMany();
    }

    /** Solo los productos que se publican en la tienda. */
    public function scopeVisibles(Builder $consulta): Builder
    {
        return $consulta->where('visible', true);
    }

    /** Productos con el stock en el mínimo o por debajo. */
    public function scopeStockBajo(Builder $consulta): Builder
    {
        return $consulta->whereColumn('stock', '<=', 'stock_minimo');
    }

    /** Precio que paga el cliente: el de oferta si tiene, si no el normal. */
    public function precioFinal(): float
    {
        return $this->enOferta() ? $this->precio_oferta : $this->precio;
    }

    public function enOferta(): bool
    {
        return $this->precio_oferta !== null && $this->precio_oferta > 0 && $this->precio_oferta < $this->precio;
    }

    /** Porcentaje de descuento redondeado, o 0 si no está en oferta. */
    public function descuento(): int
    {
        return $this->enOferta() ? (int) round((1 - $this->precio_oferta / $this->precio) * 100) : 0;
    }

    /** "agotado", "bajo" o "disponible", según el stock. */
    public function estadoStock(): string
    {
        if ($this->stock === 0) {
            return 'agotado';
        }

        return $this->stock <= $this->stock_minimo ? 'bajo' : 'disponible';
    }

    /** Dirección completa de la foto, o null si no tiene. */
    public function urlFoto(): ?string
    {
        return $this->foto ? asset($this->foto) : null;
    }

    /**
     * Cambia el stock y deja el movimiento en el historial del inventario.
     * $cantidad es positiva si entra mercadería y negativa si sale.
     */
    public function moverStock(int $cantidad, string $tipo, ?string $motivo = null, ?int $usuarioId = null, ?int $pedidoId = null): MovimientoInventario
    {
        $this->stock = max(0, $this->stock + $cantidad);
        $this->save();

        return $this->movimientos()->create([
            'user_id' => $usuarioId,
            'pedido_id' => $pedidoId,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'stock_resultante' => $this->stock,
            'motivo' => $motivo,
        ]);
    }
}
