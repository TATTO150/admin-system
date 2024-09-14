<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAsignacion extends Model
{ use HasFactory;

    protected $table = 'tbl_tipos_asignacion';
    protected $primaryKey = 'COD_TIPO_ASIGNACION';

    protected $fillable = [
        'TIPO_ASIGNACION'
    ];

    public $timestamps = false;

    // Relación con Asignacion_Equipos (si es necesario)
    public function asignaciones()
    {
        return $this->hasMany(Asignacion_Equipos::class, 'TIPO_ASIGNACION', 'COD_TIPO_ASIGNACION');
    }
}
