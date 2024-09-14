<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEquipo extends Model
{
    use HasFactory;

    protected $table = 'tbl_tipo_equipo';
    protected $primaryKey = 'COD_TIP_EQUIPO';
    
    protected $fillable = [
        'TIPO_EQUIPO',
        'PROTEGIDO'
    ];

    public $timestamps = false;
    public function equipos()
{
    return $this->hasMany(Equipos::class, 'COD_TIP_EQUIPO', 'COD_TIP_EQUIPO');
}
}
