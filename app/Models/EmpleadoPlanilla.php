<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpleadoPlanilla extends Model
{
    use HasFactory;

    protected $table = 'tbl_empleado_planilla'; // Especifica la tabla correspondiente
    protected $primaryKey = 'COD_EMPLEADO_PLANILLA';
    
    protected $fillable = [
        'COD_EMPLEADO_PLANILLA', 
        'COD_EMPLEADO', 
        'COD_PLANILLA', 
        'SALARIO_BASE', 
        'DEDUCCIONES', 
        'SALARIO_NETO'
    ]; // Ajusta los campos según tu esquema de base de datos
    
    public $timestamps = false; // Deshabilita las marcas de tiempo

     

    public function planilla()
    {
        return $this->belongsTo(Planillas::class, 'COD_PLANILLA', 'COD_PLANILLA');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleados::class, 'COD_EMPLEADO', 'COD_EMPLEADO');
    }
}
