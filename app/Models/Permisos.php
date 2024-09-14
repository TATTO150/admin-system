<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permisos extends Model
{
    use HasFactory;

    protected $table = 'tbl_permisos'; // Especifica la tabla correspondiente en este caso Permisos
    protected $primaryKey = 'COD_PERMISOS';
    
    protected $fillable = [
        'COD_PERMISOS', 
        'Id_Rol',
        'Id_Objeto',
        'Permiso_Insercion',
        'Permiso_Eliminacion',
        'Permiso_Actualizacion',
        'Permiso_Consultar'
    ]; // Ajusta los campos según tu esquema de base de datos
    
    public $timestamps = false; // Deshabilita las marcas de tiempo
     // Definición de la relación con el modelo Rol
     public function rol()
     {
         return $this->belongsTo(Rol::class, 'Id_Rol', 'Id_Rol');
     }
}