<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Parlante extends Model
{
    protected $table = 'parlantes';

    protected $fillable = [
        'nombre', 'slug', 'tipo', 'potencia_w', 'bateria_h', 'resistencia',
        'foto', 'foto_alt', 'sello', 'visible', 'orden',
    ];

    protected $casts = [
        'potencia_w' => 'integer',
        'bateria_h' => 'integer',
        'visible' => 'boolean',
        'orden' => 'integer',
    ];

    /** Solo los parlantes que se publican en la landing. */
    public function scopeVisibles(Builder $consulta): Builder
    {
        return $consulta->where('visible', true);
    }

    /** Dirección completa de la foto, o null si no tiene. */
    public function urlFoto(): ?string
    {
        return $this->foto ? asset($this->foto) : null;
    }
}
