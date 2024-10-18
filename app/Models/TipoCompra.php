<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCompra extends Model
{ 
    use HasFactory;

    protected $table = 'tbl_tipo_compra';
    protected $primaryKey = 'COD_TIPO';
    public $timestamps = false;

    protected $fillable = [
        'DESC_TIPO',
    ];
}
