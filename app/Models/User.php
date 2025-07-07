<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'usuario',
        'email',
        'password',
        'rol_id',
        'genero',
    ];

    protected $hidden = [
        'password',
    ];

    // 🔧 Relación con la tabla roles
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
}
