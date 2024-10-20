<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoCompra extends Model
{
    use HasFactory;

    protected $table = 'tbl_estado_compra';
    protected $primaryKey = 'COD_ESTADO';
    public $timestamps = false;

    protected $fillable = [
        'DESC_ESTADO',
    ];
}
