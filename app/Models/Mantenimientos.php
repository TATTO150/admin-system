<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimientos extends Model
{
    use HasFactory;

    protected $table = 'tbl_mantenimiento';
    protected $primaryKey = 'COD_MANTENIMIENTO';
    
    protected $fillable = [
        'COD_EMPLEADO',
        'COD_ESTADO_MANTENIMIENTO',
        'COD_EQUIPO',
        'DESC_MANTENIMIENTO',
        'FEC_INGRESO',
        'FEC_FINAL_PLANIFICADA',
        'FEC_FINAL_REAL'
    ];

    public $timestamps = false;

    public function empleado()
    {
        return $this->belongsTo(Empleados::class, 'COD_EMPLEADO', 'COD_EMPLEADO');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipos::class, 'COD_EQUIPO', 'COD_EQUIPO');
    }

    public function estado_mantenimiento()
    {
        return $this->belongsTo(Estado_Mantenimiento::class, 'COD_ESTADO_MANTENIMIENTO', 'COD_ESTADO_MANTENIMIENTO');
    }
}