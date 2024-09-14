<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametros extends Model
{
    use HasFactory;

    protected $table = 'tbl_ms_parametros'; // Especifica la tabla correspondiente en este caso Bitacora
    protected $primaryKey = 'Id_Parametro';
    
    protected $fillable = ['Id_Parametro', 'Parametro', 'Valor', 'Id_Usuario', 'Fecha_Creacion', 'Fecha_Modificacion']; // Ajusta los campos según tu esquema de base de datos
    
    public $timestamps = false; // Deshabilita las marcas de tiempo
    public function estaProtegido()
    {
        return $this->Valor == 1;
    }
}