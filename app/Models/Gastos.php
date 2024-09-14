<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gastos extends Model
{
    use HasFactory;

    protected $table = 'tbl_gastos'; 
    protected $primaryKey = 'COD_GASTO';
    
    protected $fillable = [
        'COD_GASTO', 
        'COD_COMPRA', 
        'COD_PROYECTO', 
        'FEC_REGISTRO', 
        'SUBTOTAL',
        'TOTAL'
    ]; 
    
    public $timestamps = false; 
     // Relación con Compras
     public function compra()
     {
         return $this->belongsTo(Compras::class, 'COD_COMPRA', 'COD_COMPRA');
     }
 
     // Relación con Proyectos
     public function proyecto()
     {
         return $this->belongsTo(Proyectos::class, 'COD_PROYECTO', 'COD_PROYECTO');
     }

}