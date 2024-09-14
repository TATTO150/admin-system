<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion_Equipos extends Model
{
    use HasFactory;

    protected $table = 'tbl_equipo_asignacion';
    protected $primaryKey = 'COD_ASIGNACION_EQUIPO';
    
    protected $fillable = [
        'COD_EMPLEADO',
        'COD_EQUIPO',
        'COD_PROYECTO',
        'DESCRIPCION',
        'COD_ESTADO_ASIGNACION',
        'FECHA_ASIGNACION_INICIO',
        'FECHA_ASIGNACION_FIN',
        'TIPO_ASIGNACION' // Incluye el nuevo campo aquí
    ];

    public $timestamps = false;

    // Definir la relación con empleados
    public function empleado()
    {
        return $this->belongsTo(Empleados::class, 'COD_EMPLEADO', 'COD_EMPLEADO');
    }

    // Definir la relación con equipos
    public function equipo()
    {
        return $this->belongsTo(Equipos::class, 'COD_EQUIPO', 'COD_EQUIPO');
    }
    
    // Definir la relación con proyectos
    public function proyectos()
    {
        return $this->belongsTo(Proyectos::class, 'COD_PROYECTO', 'COD_PROYECTO');
    }

    // Definir la relación con estado de asignación
    public function estado_asignacion()
    {
        return $this->belongsTo(EstadoAsignacion::class, 'COD_ESTADO_ASIGNACION', 'COD_ESTADO_ASIGNACION');
    }

    // Definir la relación con el nuevo modelo TipoAsignacion
    public function tipoAsignacion()
    {
        return $this->belongsTo(TipoAsignacion::class, 'TIPO_ASIGNACION', 'COD_TIPO_ASIGNACION');
    }
}
