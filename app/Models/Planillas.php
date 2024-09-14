<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planillas extends Model
{
    use HasFactory;

    protected $table = 'tbl_planilla'; // Especifica la tabla correspondiente
    protected $primaryKey = 'COD_PLANILLA';
    
    protected $fillable = ['COD_PLANILLA', 'FECHA_GENERADA', 'TOTAL_PAGADO', 'MES', 'COD_TIPO_PLANILLA']; // Ajusta los campos según tu esquema de base de datos
    
    public $timestamps = false; // Deshabilita las marcas de tiempo
    
    public function empleadosPlanilla()
    {
        return $this->hasMany(EmpleadoPlanilla::class, 'COD_PLANILLA', 'COD_PLANILLA');
    }

    // Elimina o comenta estas relaciones si ya no son necesarias
    // public function proyectos()
    // {
    //     return $this->hasMany(Proyectos::class, 'COD_PROYECTO', 'COD_PROYECTO');
    // }

    // public function proyecto()
    // {
    //     return $this->belongsTo(Proyectos::class, 'COD_PROYECTO', 'COD_PROYECTO');
    // }
}
