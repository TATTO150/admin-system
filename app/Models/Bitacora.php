<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;

    protected $table = 'tbl_bitacora'; // Especifica la tabla correspondiente en este caso Bitacora
    protected $primaryKey = 'ID_bitacora';
    
    protected $fillable = ['ID_bitacora', 'Id_usuario', 'Id_Objetos', 'Descripcion', 'Fecha', 'Accion']; // Ajusta los campos según tu esquema de base de datos
    
    public $timestamps = false; // Deshabilita las marcas de tiempo
    public function user()
    {
        return $this->belongsTo(User::class, 'Id_usuario', 'Id_usuario');
    }

    // Relación con el modelo Objeto
    public function objeto()
    {
        return $this->belongsTo(Objeto::class, 'Id_Objetos', 'Id_Objetos');
    }
}