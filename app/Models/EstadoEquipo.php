<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoEquipo extends Model
{use HasFactory;

    protected $table = 'tbl_estado_equipo';
    protected $primaryKey = 'COD_ESTADO_EQUIPO';
    
    protected $fillable = [
        'DESC_ESTADO_EQUIPO'
    ];

    public $timestamps = false;
}
