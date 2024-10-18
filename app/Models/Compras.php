<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compras extends Model
{
    use HasFactory;

    protected $table = 'tbl_compra'; 
    protected $primaryKey = 'COD_COMPRA';
    
    protected $fillable = [
        'COD_COMPRA',
        'Id_usuario', 
        'DESC_COMPRA',
        'COD_PROYECTO', 
        'FEC_REGISTRO',
        'COD_ESTADO', 
        'COD_TIPO', 
        'PRECIO_COMPRA',
        'PRECIO_CUOTA',
        'PRECIO_NETO',
        'CUOTAS_PAGADAS',
        'TOTAL_CUOTAS',
        'FECHA_PAGO',
        'LIQUIDEZ_COMPRA',
    ]; 

    protected $dates = ['FEC_REGISTRO'];
    
    public $timestamps = false; 
    
    public function proyecto()
    {
        return $this->hasMany(Proyectos::class, 'COD_PROYECTO', 'COD_PROYECTO');
    }


    // Relación con Gastos
    public function gastos()
    {
        return $this->hasMany(Gastos::class, 'COD_COMPRA', 'COD_COMPRA');
    }


}
