<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpleadoProyectos extends Model
{
    use HasFactory;

    protected $table = 'tbl_empleado_proyectos'; // Especifica la tabla correspondiente en este caso Area
    protected $primaryKey = 'COD_EMPLE_PROYECTO';
    
    protected $fillable = [
        'COD_EMPLE_PROYECTO', 
        'COD_PROYECTO',
        'DNI_EMPLEADO',
    ]; 
    
    public $timestamps = false; // Deshabilita las marcas de tiempo

    public function solicitud()
    {
        return $this->belongsTo(solitudes::class, 'COD_AREA', 'COD_AREA');
    }
    public function empleado()
    {
        return $this->belongsTo(Empleados::class, 'DNI_EMPLEADO', 'DNI_EMPLEADO');

    }

    public function proyectos()
    {
        return $this->belongsTo(Proyectos::class, 'COD_PROYECTO', 'COD_PROYECTO');
    }




}