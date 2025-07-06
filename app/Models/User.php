<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios'; // ← usa tu tabla real

    public $timestamps = false; // ← si no usas created_at / updated_at

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
}
