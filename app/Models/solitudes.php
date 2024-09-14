<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class solitudes extends Model
{
    use HasFactory;

    protected $table = 'tbl_solicitudes'; // Especifica la tabla correspondiente en este caso Bitacora
    protected $primaryKey = 'COD_SOLICITUD';
    
    protected $fillable = ['COD_SOLICITUD', 'COD_EMPLEADO', 'DESC_SOLICITUD', 'COD_AREA', '	COD_PROYECTO', 'ESTADO_SOLICITUD', 'PRESUPUESTO_SOLICITUD']; // Ajusta los campos según tu esquema de base de datos
    
    public $timestamps = false; // Deshabilita las marcas de tiempo

    public function empleado()
{
    return $this->belongsTo(Empleados::class, 'COD_EMPLEADO', 'COD_EMPLEADO');
}

public function area()
{
    return $this->belongsTo(Area::class, 'COD_AREA', 'COD_AREA');
}

public function proyecto()
{
    return $this->belongsTo(Proyectos::class, 'COD_PROYECTO', 'COD_PROYECTO');
}
}

