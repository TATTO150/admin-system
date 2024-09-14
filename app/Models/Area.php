<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'tbl_area'; // Especifica la tabla correspondiente en este caso Area
    protected $primaryKey = 'COD_AREA';
    
    protected $fillable = [
        'COD_AREA', 
        'NOM_AREA',
        'ESTADO'
    ]; // Ajusta los campos según tu esquema de base de datos
    
    public $timestamps = false; // Deshabilita las marcas de tiempo

    public function solicitud()
    {
        return $this->belongsTo(solitudes::class, 'COD_AREA', 'COD_AREA');
    }

}