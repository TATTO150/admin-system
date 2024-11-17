<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesiones extends Model
{
    use HasFactory;

    protected $table = 'sessions'; // Especifica la tabla correspondiente
    protected $fillable = ['id', 'user_id', 'ip_address', 'user_agent', 'payload', 'last_activity']; // Ajusta los campos según tu esquema de base de datos

    public $timestamps = false;
    
}