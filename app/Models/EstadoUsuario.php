<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoUsuario extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla asociada a este modelo
    protected $table = 'tbl_estado_usuario';

    // Especifica el nombre de la clave primaria si no sigue el estándar "id"
    protected $primaryKey = 'COD_ESTADO';

    // Especifica si la clave primaria es auto-incremental
    public $incrementing = true;

    // Especifica el tipo de datos de la clave primaria
    protected $keyType = 'int';

    // Si la tabla no tiene las columnas created_at y updated_at
    public $timestamps = false;

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'ESTADO',
        'DESCRIPCION',
    ];
    public function relatedRecords()
    {
        // Example: Return related users for this estado
        return $this->hasMany(User::class, 'Estado_Usuario', 'COD_ESTADO');
    }
}
