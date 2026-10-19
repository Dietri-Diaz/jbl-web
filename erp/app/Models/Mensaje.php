<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensajes';

    protected $fillable = ['nombre', 'correo', 'mensaje', 'leido'];

    protected $casts = [
        'leido' => 'boolean',
    ];

    public function scopeNoLeidos(Builder $consulta): Builder
    {
        return $consulta->where('leido', false);
    }
}
