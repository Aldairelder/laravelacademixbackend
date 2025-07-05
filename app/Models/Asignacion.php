<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    protected $table = 'asignaciones';
    protected $primaryKey = 'id_asignacion';
    public $timestamps = false;
    protected $guarded = [];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id', 'id_curso');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id', 'id_grupo');
    }
}
