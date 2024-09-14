<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objeto extends Model
{
    use HasFactory;

    protected $table = 'tbl_objeto'; // Especifica la tabla correspondiente en este caso Bitacora
    protected $primaryKey = 'Id_Objetos';
    
    protected $fillable = ['Id_Objetos', 'Objeto', 'Descripcion', 'Tipo_Objeto']; // Ajusta los campos según tu esquema de base de datos
    
    public $timestamps = false; // Deshabilita las marcas de tiem
}
