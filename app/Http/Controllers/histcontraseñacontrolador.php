<?php

namespace App\Http\Controllers;

use App\Models\HistContrasena;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Laravel\Fortify\Rules\Validaciones;
use Illuminate\Support\Str;
use App\Models\User;
use App\Http\Controllers\BitacoraController;
use Illuminate\Support\Facades\Auth;

class histcontraseñacontrolador extends Controller
{
   /* protected $bitacora;

    public function __construct(BitacoraController $bitacora)
    {
        $this->bitacora = $bitacora;
    }*/

    public static function generatePassword()
    {
        $Contrasena = Str::random(8);

        return Hash::make($Contrasena);
    }

    public function contrasena($user)
    {
        $Contrasena = $this->generatePassword();
    
        HistContrasena::create([
            'Id_usuario' => $user->Id_usuario,
            'Contrasena' => $Contrasena,
        ]);
    
       /* $this->bitacora->registrarEnBitacora(Auth::id(), 9, 'Contraseña generada y almacenada', 'Insert');*/
    }
}
