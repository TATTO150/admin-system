<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipos extends Model
{
    use HasFactory;

    protected $table = 'tbl_equipo'; 
    protected $primaryKey = 'COD_EQUIPO';
    
    protected $fillable = [
        'COD_EQUIPO', 
        'NOM_EQUIPO', 
        'COD_TIP_EQUIPO', 
        'DESC_EQUIPO', 
        'COD_ESTADO_EQUIPO', 
        'FECHA_COMPRA',
        'VALOR_COMPRA'
    ]; 
    
    public $timestamps = false;

    // Definir la relación con TipoEquipo
    public function tipoEquipo()
    {
        return $this->belongsTo(TipoEquipo::class, 'COD_TIP_EQUIPO');
    }

    public function estadoEquipo()
    {
        return $this->belongsTo(EstadoEquipo::class, 'COD_ESTADO_EQUIPO');
    }
}
