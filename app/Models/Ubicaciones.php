<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubicaciones extends Model
{
    use HasFactory;

    protected $table = 'tbl_ubicacion'; 
    protected $primaryKey = 'COD_UBICACION';
    
    protected $fillable = [
        'COD_UBICACION', 
        'NOM_UBICACION',
        'DESCRIPCION'
    ]; 
    
    public $timestamps = false; 

    public function asignacion()
    {
        return $this->hasMany(Asignacion_Equipo::class, 'COD_UBICACION', 'COD_UBICACION');
    }
    
   
}