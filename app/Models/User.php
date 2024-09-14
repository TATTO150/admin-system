<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasProfilePhoto, TwoFactorAuthenticatable;

    protected $table = 'tbl_ms_usuario';
    protected $primaryKey = 'Id_usuario';

    public $timestamps = false;

    protected $fillable = [
        'Usuario',
        'Nombre_Usuario',
        'Estado_Usuario',
        'Contrasena',
        'Id_Rol',
        'Fecha_Ultima_Conexion',
        'Primer_Ingreso',
        'Fecha_Vencimiento',
        'Correo_Electronico',
        'Verificacion_Usuario',
        'Intentos_Login',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at'
    ];

    protected $hidden = [
        'Contrasena',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected $casts = [
        'Fecha_Ultima_Conexion' => 'datetime',
        'Primer_Ingreso' => 'integer',
        'Fecha_Vencimiento' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            if ($user->getKey() === 1) {
                throw new \Exception('No se puede eliminar el primer usuario.');
            }
        });
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    public function getEmailForVerification()
    {
        return $this->Correo_Electronico;
    }

    public function getKey()
    {
        return $this->getAttribute($this->primaryKey);
    }

    public function getAuthPassword()
    {
        return $this->Contrasena;
    }

    public function getEmailForPasswordReset()
    {
        return $this->Correo_Electronico;
    }

    public function username()
    {
        return 'Correo_Electronico';
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function markEmailAsVerified()
    {
        $this->Verificacion_Correo_Electronico = true;
        $this->save();
    }

    /**
     * Determine if two-factor authentication is enabled for this user.
     *
     * @return bool
     */
    public function hasTwoFactorEnabled()
    {
        return !is_null($this->two_factor_secret);
    }
    public function rol()
   {
    return $this->belongsTo(Rol::class, 'Id_Rol', 'Id_Rol');
   }
   public function estado()
{
    return $this->belongsTo(EstadoUsuario::class, 'Estado_Usuario', 'COD_ESTADO');
}
public function setEstadoUsuarioAttribute($estado)
{
    // Busca el estado en la tabla de estados
    $estadoUsuario = EstadoUsuario::where('ESTADO', $estado)->first();

    if ($estadoUsuario) {
        // Asigna el COD_ESTADO correspondiente al campo Estado_Usuario
        $this->attributes['Estado_Usuario'] = $estadoUsuario->COD_ESTADO;
    } else {
        throw new \Exception("El estado '{$estado}' no existe en la tabla tbl_estado_usuario.");
    }
}
}
