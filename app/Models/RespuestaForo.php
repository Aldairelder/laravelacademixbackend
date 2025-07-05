<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestaForo extends Model
{
    protected $table = 'respuestas_foro';
    protected $primaryKey = 'id_respuesta';
    public $timestamps = false;
    protected $guarded = [];
}
