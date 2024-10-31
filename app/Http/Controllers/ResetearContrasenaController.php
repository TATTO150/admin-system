<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use App\Rules\Validaciones;
use Carbon\Carbon;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\PasswordResetNotification;


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
                'code' => 'required|string',
                'password' => [
                    'required',
                    (new Validaciones)->requerirSinEspacios()->requerirSimbolo()->requerirMinuscula()->requerirMayuscula()->requerirNumero()->requerirlongitudMinima(8)->requerirlongitudMaxima(12)->requerirCampo(),
                    'confirmed', // Agrega la validación de confirmación de contraseña
                ],
            ], [
                'password.confirmed' => 'Las contraseñas no son iguales. Asegúrese de que la confirmación coincida con la nueva contraseña.' // Mensaje personalizado
            ]);
            
            $user = User::where('Correo_Electronico', $request->input('email'))
                ->orWhere('Usuario', $request->input('email'))
                ->first();
            
            if (!$user) {
                $this->registrarEnBitacora(null, 3, 'Intento de reseteo de contraseña fallido - usuario no encontrado', 'Error');
                throw ValidationException::withMessages([
                    'email' => [__('No se pudo encontrar un usuario con ese correo electrónico.')],
                ]);
            }
    
            if (Hash::check($request->input('password'), $user->Contrasena)) {
                return redirect()->back()->withErrors(['password' => 'La nueva contraseña no puede ser la misma que la contraseña anterior.']);
            }
    
            if (empty($user->two_factor_secret) || 
                !$this->provider->verify(decrypt($user->two_factor_secret), $request->input('code'))) {
                
                $user->Intentos_OTP += 1;
                $user->save(); // Guardar los cambios en la base de datos
    
                if ($user->Intentos_OTP >= 3) {
                    $user->Estado_Usuario = 'BLOQUEADO';
                    $user->save(); // Guardar el cambio de estado
    
                    return redirect()->route('bloqueo')->with('error', __('Su cuenta ha sido bloqueada por demasiados intentos fallidos.'));
                }
    
                $this->registrarEnBitacora($user->Id_usuario, 3, 'Intento de reseteo de contraseña fallido - código OTP incorrecto', 'Error');
    
                throw ValidationException::withMessages([
                    'code' => [__('El código OTP ingresado es incorrecto.')],
                ]);
            }
    
            // Actualizar la contraseña del usuario
            $user->Contrasena = Hash::make($request->input('password'));
            $user->save();
    
            // Actualizar estado del usuario según condiciones
            if ($user->Id_usuario == 1 && $user->Estado_Usuario == 'BLOQUEADO') {
                $user->Estado_Usuario = 'ACTIVO';
                $user->Fecha_Vencimiento = \Carbon\Carbon::now()->addMonths(6);
            } elseif ($user->Estado_Usuario === 'BLOQUEADO' || $user->Estado_Usuario === '3') {
                $user->Estado_Usuario = 'ACTIVO'; // Cambia el estado de usuario bloqueado a 'ACTIVO'
            } elseif ($user->Id_Rol == 3) {
                $user->Estado_Usuario = 'NUEVO';
            } else {
                $user->Estado_Usuario = 'RESETEO';
            }
    
            $user->Intentos_Login = 0;
            $user->Intentos_OTP = 0;
            $user->save();
    
            // Enviar notificación de restablecimiento a administradores
            $adminEmails = User::where('Id_Rol', 1)->pluck('Correo_Electronico')->toArray();
    
            if (count($adminEmails) > 0) {
                Mail::to($adminEmails)->send(new PasswordResetNotification($user));
            }
    
            $this->registrarEnBitacora($user->Id_usuario, 3, 'Usuario desbloqueado correctamente y contraseña restablecida', 'Update');
    
            return redirect()->route('login')->with('status', __('Contraseña restablecida correctamente. Inicie sesión con su nueva contraseña.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->registrarEnBitacora($user->Id_usuario ?? null, 3, 'Error desconocido al intentar restablecer la contraseña', 'Error');
            throw ValidationException::withMessages([
                'error' => [__('Hubo un error al intentar restablecer la contraseña.')],
            ]);
        }
    }
    

     // Enviar el enlace de restablecimiento de contraseña
    public function sendResetLink(Request $request)
    {
        // Validar el campo 'Correo_Electronico'
        $request->validate([
            'Correo_Electronico' => 'required|email',
        ]);

        // Buscar si el usuario existe usando 'Correo_Electronico'
        $user = User::where('Correo_Electronico', $request->Correo_Electronico)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['Correo_Electronico' => 'No existe un usuario con este correo.']);
        }

        // Generar un token de restablecimiento
        $token = Str::random(60);

        // Almacenar el token en la tabla password_resets
        DB::table('password_reset_tokens')->insert([
            'email' => $user->Correo_Electronico,
            'token' => $token,
            'created_at' => now(),
        ]);

        // Enviar el correo con el enlace de restablecimiento
        Mail::to($user->Correo_Electronico)->send(new ResetPasswordMail($user, $token));

        // Redirigir al login con un mensaje
        return redirect()->route('login')->with('status', 'Se ha enviado un enlace a tu correo para restablecer tu contraseña.');
    }


    public function showEmailForm()
        {
            return view('auth.reset_password_request'); // La vista donde se ingresa el correo
        }

 // Mostrar la vista de restablecimiento de contraseña
 public function showResetForm($token)
 {
     return view('auth.reset_password_form', ['token' => $token]);
 }

 // Actualizar la contraseña
public function resetPassword(Request $request)
{
    $request->validate([
        'Contrasena' => [
            'required',
            'confirmed',
            (new Validaciones)->requerirSinEspacios()->requerirSimbolo()->requerirMinuscula()->requerirMayuscula()->requerirNumero()->requerirlongitudMinima(8)->requerirlongitudMaxima(12)->requerirCampo(),
        ],
        'token' => 'required',
    ], [
        'Contrasena.confirmed' => 'La confirmación de la contraseña no coincide.', // Mensaje personalizado
    ]);

    // Buscar el registro que coincide con el token
    $resetRecord = DB::table('password_reset_tokens')
        ->where('token', $request->token)
        ->first();

    // Verificar si existe el token
    if (!$resetRecord) {
        return back()->withErrors(['error' => 'Token inválido o expirado.']);
    }

    // Obtener el correo electrónico asociado al token
    $email = $resetRecord->email;

    // Buscar el usuario en la tabla tbl_ms_usuario usando el correo electrónico obtenido
    $user = User::where('Correo_Electronico', $email)->first();

    // Verificar si existe el usuario
    if (!$user) {
        return back()->withErrors(['error' => 'No se encontró un usuario con ese correo electrónico.']);
    }

    // Validar que la nueva contraseña no sea igual a la anterior
    if (Hash::check($request->Contrasena, $user->Contrasena)) {
        return back()->withErrors(['error' => 'La nueva contraseña no puede ser igual a la anterior.']);
    }

    // Actualizar la contraseña del usuario
    $user->Contrasena = bcrypt($request->Contrasena);
    $user->Intentos_OTP = 0;
    $user->save();

    // Lógica para cambiar el estado del usuario si es necesario
    if ($user->Id_usuario == 1 && $user->Estado_Usuario == 'BLOQUEADO') {
        $user->Estado_Usuario = 'ACTIVO';
        $user->Fecha_Vencimiento = \Carbon\Carbon::now()->addMonths(6);
        $user->save();
    } elseif ($user->Estado_Usuario == 'BLOQUEADO' || $user->Estado_Usuario == 3) {
        $user->Estado_Usuario = 'RESETEO';
        $user->save();
    } elseif ($user->Id_Rol == 3) {
        $user->Estado_Usuario = 'NUEVO';
        $user->save();
    } else {
        $user->Estado_Usuario = 'RESETEO';
        $user->save();
    }

    // Eliminar el token de la tabla password_reset_tokens
    DB::table('password_reset_tokens')->where('token', $request->token)->delete();

    // Redirigir al login con un mensaje de éxito
    return redirect()->route('login')->with('status', 'Tu contraseña ha sido restablecida con éxito.');
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

