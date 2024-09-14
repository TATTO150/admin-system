<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPlanilla extends Model
{
    use HasFactory;

    protected $table = 'tbl_tipo_planilla'; 
    protected $primaryKey = 'COD_TIPO_PLANILLA';
    
    protected $fillable = [
        'COD_TIPO_PLANILLA', 
        'TIPO_PLANILLA',
        'DESCRIPCION'
    ]; 
    
    public $timestamps = false; 
}