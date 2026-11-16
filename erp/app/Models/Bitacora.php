<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro de lo que hace cada usuario en el panel.
 * Uso: Bitacora::registrar('crear', 'productos', 'Agregó el JBL Flip 5');
 */
class Bitacora extends Model
{
    protected $table = 'bitacora';

    public const UPDATED_AT = null;

    public const ACCIONES = [
        'ingresar' => 'Ingreso',
        'salir' => 'Salida',
        'crear' => 'Creación',
        'editar' => 'Edición',
        'eliminar' => 'Eliminación',
        'estado' => 'Cambio de estado',
        'stock' => 'Movimiento de stock',
    ];

    protected $fillable = ['user_id', 'usuario', 'accion', 'modulo', 'descripcion', 'ip'];

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function nombreAccion(): string
    {
        return self::ACCIONES[$this->accion] ?? $this->accion;
    }

    public static function registrar(string $accion, string $modulo, string $descripcion): self
    {
        $usuario = auth()->user();

        return self::create([
            'user_id' => $usuario?->id,
            'usuario' => $usuario?->name ?? 'Tienda',
            'accion' => $accion,
            'modulo' => $modulo,
            'descripcion' => mb_substr($descripcion, 0, 255),
            'ip' => request()->ip(),
        ]);
    }
}
