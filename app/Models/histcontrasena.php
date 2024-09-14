<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class histcontrasena extends Model
{
    use HasFactory;

    protected $table = 'tbl_ms_hist_contraseña'; // Especifica la tabla correspondiente en este caso Bitacora
    protected $primaryKey = 'Id_Hist';
    
    protected $fillable = ['Id_usuario', 'Contrasena'];
    // Ajusta los campos según tu esquema de base de datos
    
    public $timestamps = false; // Deshabilita las marcas de tiem
     // Relación con el modelo User
     public function usuario()
     {
         return $this->belongsTo(User::class, 'Id_usuario');
     }
}

