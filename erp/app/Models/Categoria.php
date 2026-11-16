<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = ['nombre', 'slug', 'descripcion', 'visible', 'orden'];

    protected $casts = [
        'visible' => 'boolean',
        'orden' => 'integer',
    ];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    /** Solo las categorías que se muestran en la tienda. */
    public function scopeVisibles(Builder $consulta): Builder
    {
        return $consulta->where('visible', true);
    }
}
