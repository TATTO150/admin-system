<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleados extends Model
{
    use HasFactory;

protected $table = 'tbl_empleado'; // Especifica la tabla correspondiente
    protected $primaryKey = 'COD_EMPLEADO';
    
    protected $fillable = [
        'COD_EMPLEADO', 
        'NOM_EMPLEADO', 
        'COD_ESTADO_EMPLEADO', 
        'COD_AREA', 
        'DNI_EMPLEADO', 
        'LICENCIA_VEHICULAR', 
        'COD_CARGO', 
        'FEC_INGRESO_EMPLEADO',
        'CORREO_EMPLEADO',
        'DIRECCION_EMPLEADO',
        'CONTRATO_EMPLEADO',
        'COD_PROYECTO',
        'SALARIO_BASE',
        'DEDUCCIONES',
        'SALARIO_NETO',
        'ESTADO_EMPLEADO',
        'FECHA_SALIDA'
    ]; 
    
    public $timestamps = false; // Deshabilita las marcas de tiempo

    public function estadoEmpleado()
    {
        return $this->belongsTo(EstadoEmpleado::class, 'COD_ESTADO_EMPLEADO', 'COD_ESTADO_EMPLEADO');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'COD_AREA');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'COD_CARGO');
    }

    public function solicitud()
    {
        return $this->belongsTo(Solitudes::class, 'COD_EMPLEADO', 'COD_EMPLEADO');
    }
    
    public function planillas()
    {
        return $this->hasMany(Planillas::class, 'COD_EMPLEADO', 'COD_EMPLEADO');
    }
    
    public function empleadoPlanilla()
    {
        return $this->hasMany(EmpleadoPlanilla::class, 'COD_EMPLEADO', 'COD_EMPLEADO');
    }

    public function mantenimiento()
    {
        return $this->hasMany(Mantenimientos::class, 'COD_EMPLEADO', 'COD_EMPLEADO');
    }

    public function asignacion()
    {
        return $this->hasMany(Asignacion_Equipos::class, 'COD_EMPLEADO', 'COD_EMPLEADO');
    }
   // Relación con proyectos a través de DNI_EMPLEADO
   public function proyectos()
   {
       return $this->belongsToMany(Proyectos::class, 'tbl_empleado_proyectos', 'DNI_EMPLEADO', 'COD_PROYECTO');
   }


}
