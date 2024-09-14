<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    protected $table = 'tbl_cargos'; // Especifica la tabla correspondiente en este caso Cargo
    protected $primaryKey = 'COD_CARGO';
    
    protected $fillable = [
        'COD_CARGO', 
        'NOM_CARGO',
        'SALARIOS',
        'FUNCION_PRINCIPAL',
        'ESTADO'
    ]; // Ajusta los campos según tu esquema de base de datos
    
    public $timestamps = false; // Deshabilita las marcas de tiempo
}