<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado_Mantenimiento extends Model
{
    use HasFactory;

    protected $table = 'tbl_estado_mantenimiento';
    protected $primaryKey = 'COD_ESTADO_MANTENIMIENTO';
    
    protected $fillable = [
        'ESTADO'
    ];

    public $timestamps = false;

    public function mantenimientos()
    {
        return $this->hasMany(Mantenimientos::class, 'COD_ESTADO_MANTENIMIENTO', 'COD_ESTADO_MANTENIMIENTO');
    }
}