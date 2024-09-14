<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyectos extends Model
{
    use HasFactory;

    protected $table = 'tbl_proyectos';
    protected $primaryKey = 'COD_PROYECTO';
    
    protected $fillable = ['COD_PROYECTO', 'NOM_PROYECTO', 'FEC_INICIO', 'FEC_FINAL', 'DESC_PROYECTO', 'PRESUPUESTO_INICIO', 'ESTADO_PROYECTO'];
    public $timestamps = false;
    public function empleados()
    {
        return $this->belongsToMany(Empleados::class, 'tbl_empleado_proyectos', 'COD_PROYECTO', 'DNI_EMPLEADO');
    }
     public function getEmpleadosAsignadosAttribute()
     {
         return EmpleadoProyectos::where('COD_PROYECTO', $this->COD_PROYECTO)
             ->pluck('DNI_EMPLEADO')
             ->toArray();
     }

    public function solicitudes()
    {
        return $this->hasMany(solitudes::class, 'COD_PROYECTO', 'COD_PROYECTO');
    }

    public function compras()
    {
        return $this->hasMany(Compras::class, 'COD_PROYECTO', 'COD_PROYECTO');
    }

    public function gastos()
    {
        return $this->hasMany(Gastos::class, 'COD_PROYECTO', 'COD_PROYECTO');
    }

}
