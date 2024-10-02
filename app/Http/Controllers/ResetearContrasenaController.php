<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use App\Rules\Validaciones;
use Carbon\Carbon;
use App\Mail\PasswordResetNotification;
use Illuminate\Support\Facades\Mail;

class ResetearContrasenaController extends Controller
{
    protected $provider;

    public function __construct(TwoFactorAuthenticationProvider $provider)
    {
        $this->provider = $provider;
    }

    public function reset(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:tbl_ms_usuario,Correo_Electronico',
                'code' => 'required|string',
                'password' => [
                    (new Validaciones)->requerirSinEspacios()->requerirSimbolo()
                    ->requerirMinuscula()->requerirMayuscula()
                    ->requerirNumero()->requerirlongitudMinima(8)
                    ->requerirlongitudMaxima(12)->requerirCampo(),
                ],
            ]);
    
            // Buscar el usuario por correo electrónico
            $user = User::where('Correo_Electronico', $request->input('email'))->first();
    
            // Verificar si el usuario existe
            if (!$user) {
                $this->registrarEnBitacora(null, 3, 'Intento de reseteo de contraseña fallido - usuario no encontrado', 'Error');
                throw ValidationException::withMessages([
                    'email' => [__('No se pudo encontrar un usuario con ese correo electrónico.')],
                ]);
            }
    
            // Verificar el código OTP
            if (empty($user->two_factor_secret) || !$this->provider->verify(decrypt($user->two_factor_secret), $request->input('code'))) {
                $this->registrarEnBitacora($user->Id_usuario, 3, 'Intento de reseteo de contraseña fallido - código OTP incorrecto', 'Error');
                throw ValidationException::withMessages([
                    'code' => [__('El código OTP ingresado es incorrecto.')],
                ]);
            }
    
            // Actualizar la contraseña del usuario
            $user->Contrasena = Hash::make($request->input('password'));
            $user->save();
    
            // Verificar y actualizar el estado del usuario
            if ($user->Estado_Usuario == 'BLOQUEADO' || $user->Estado_Usuario == 3) {
                $user->Estado_Usuario = 'RESETEO';
            } elseif ($user->Id_Rol == 3) {
                $user->Estado_Usuario = 'NUEVO';
            } else {
                $user->Estado_Usuario = 'RESETEO';
            }
    
            $user->Intentos_Login = 0;
            $user->save();
    
          // Obtener todos los correos de los administradores con Id_Rol = 1
$adminEmails = User::where('Id_Rol', 1)->pluck('Correo_Electronico')->toArray();

// Enviar el correo usando el nuevo Mailable configurado con Markdown
if (count($adminEmails) > 0) {
    Mail::to($adminEmails)->send(new \App\Mail\PasswordResetNotification($user));
}
    
            $this->registrarEnBitacora($user->Id_usuario, 3, 'Usuario desbloqueado correctamente y contraseña restablecida', 'Update');
    
            // Redirigir a la ruta de confirmación de restablecimiento de contraseña
            return redirect()->route('login')
                             ->with('status', __('Contraseña restablecida correctamente. Inicie sesión con su nueva contraseña.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            // Si ocurre un error, registrar en bitácora el problema con detalles del mensaje de error
            $this->registrarEnBitacora(
                isset($user) ? $user->Id_usuario : null, 
                3, 
                'Error desconocido al intentar restablecer la contraseña: ' . $e->getMessage(), 
                'Error'
            );
            
            return back()->withErrors(['error' => __('Hubo un error al intentar restablecer la contraseña.')]);
        }
    }
    
    /**
     * Registra un evento en la bitácora.
     *
     * @param  int|null  $Id_usuario
     * @param  int  $ID_objetos
     * @param  string  $descripcion
     * @param  string  $accion
     * @return void
     */
    protected function registrarEnBitacora($Id_usuario, $ID_objetos, $descripcion, $accion)
    {
        Bitacora::create([
            'Id_usuario' => $Id_usuario,
            'Id_Objetos' => $ID_objetos,
            'Descripcion' => $descripcion,
            'Fecha' => Carbon::now(),
            'Accion' => $accion
        ]);
    }
}

