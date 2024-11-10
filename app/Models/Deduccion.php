<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduccion extends Model
{
    use HasFactory;

    protected $table = 'tbl_deduccion'; 
    protected $primaryKey = 'COD_DEDUCCION';
    
    protected $fillable = [
        'COD_DEDUCCION', 
        'COD_COMPRA',
        'DESC_DEDUCCION',
        'VALOR_DEDUCCION'
    ];     
    public $timestamps = false; 

    public function compras()
    {
        return $this->belongsTo(Compras::class, 'COD_COMPRA', 'COD_COMPRA');
    }
}