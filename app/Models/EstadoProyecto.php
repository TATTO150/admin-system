<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoProyecto extends Model
{
    use HasFactory;

    protected $table = 'tbl_estado_proyecto';
    protected $primaryKey = 'COD_ESTADO_PROYECTO';
    public $timestamps = false;

    protected $fillable = [
        'ESTADO_PROYECTO',
    ];


}