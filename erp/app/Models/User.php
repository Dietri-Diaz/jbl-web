<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'rol', 'activo', 'ultimo_ingreso'];

    protected $hidden = ['password', 'remember_token'];

    // Un usuario nuevo es administrador y está activo salvo que se indique otra cosa.
    protected $attributes = [
        'rol' => 'admin',
        'activo' => true,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'ultimo_ingreso' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function nombreRol(): string
    {
        return config("tienda.roles.{$this->rol}", $this->rol);
    }

    /** El administrador gestiona usuarios y ve la bitácora. */
    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    /** Administradores y editores pueden crear, editar y eliminar. */
    public function puedeEditar(): bool
    {
        return in_array($this->rol, ['admin', 'editor'], true);
    }

    /** Iniciales para el avatar: "Ana Torres" -> "AT". */
    public function iniciales(): string
    {
        $partes = preg_split('/\s+/', trim($this->name)) ?: [];
        $letras = mb_substr($partes[0] ?? '', 0, 1).mb_substr($partes[1] ?? '', 0, 1);

        return mb_strtoupper($letras ?: '?');
    }
}
