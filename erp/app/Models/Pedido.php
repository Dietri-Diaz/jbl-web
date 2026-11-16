<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $table = 'pedidos';

    /** Desde cada estado, a cuáles se puede pasar. */
    public const TRANSICIONES = [
        'pendiente' => ['confirmado', 'cancelado'],
        'confirmado' => ['enviado', 'cancelado'],
        'enviado' => ['entregado', 'cancelado'],
        'entregado' => [],
        'cancelado' => [],
    ];

    protected $fillable = [
        'codigo', 'cliente_nombre', 'cliente_correo', 'cliente_telefono', 'cliente_documento',
        'entrega', 'direccion', 'distrito', 'referencia', 'pago', 'estado',
        'subtotal', 'envio', 'total', 'notas',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'envio' => 'float',
        'total' => 'float',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function nombreEstado(): string
    {
        return config("tienda.estados.{$this->estado}", $this->estado);
    }

    public function nombreEntrega(): string
    {
        return config("tienda.entregas.{$this->entrega}", $this->entrega);
    }

    public function nombrePago(): string
    {
        return config("tienda.pagos.{$this->pago}", $this->pago);
    }

    /** Estados a los que este pedido puede pasar ahora. */
    public function siguientesEstados(): array
    {
        return self::TRANSICIONES[$this->estado] ?? [];
    }

    public function puedePasarA(string $estado): bool
    {
        return in_array($estado, $this->siguientesEstados(), true);
    }

    /** Código legible a partir del id: 7 -> "JBL-000007". */
    public static function codigoPara(int $id): string
    {
        return 'JBL-'.str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }
}
