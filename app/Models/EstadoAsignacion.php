<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoAsignacion extends Model
{
    use HasFactory;

    protected $table = 'tbl_estado_asignacion';
    protected $primaryKey = 'COD_ESTADO_ASIGNACION';
    public $timestamps = false;

    protected $fillable = [
        'ESTADO',
    ];
}
