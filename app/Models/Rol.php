<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'tbl_ms_roles'; // Especifica la tabla correspondiente
    protected $fillable = ['Id_Rol', 'Rol', 'Descripcion']; // Ajusta los campos según tu esquema de base de datos

    public $timestamps = false;
    public function users()
    {
        return $this->hasMany(User::class, 'Id_Rol', 'Id_Rol');
    }
}