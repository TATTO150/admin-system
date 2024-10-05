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
                'email' => 'required|email|exists:tbl_ms_usuario,Correo_Electronico',
                'code' => 'required|string',
                'password' => [
                    'required',
                    (new Validaciones)->requerirSinEspacios()->requerirSimbolo()->requerirMinuscula()->requerirMayuscula()->requerirNumero()->requerirlongitudMinima(8)->requerirlongitudMaxima(12)->requerirCampo(),
                    'confirmed', // Agrega la validación de confirmación de contraseña
                ],
            ], [
                'password.confirmed' =>'Las contraseñas no son iguales. Asegúrese de que la confirmación coincida con la nueva contraseña.' // Mensaje personalizado
            ]);
            $user = User::where('Correo_Electronico', $request->input('email'))->first();

            if (!$user) {
                $this->registrarEnBitacora(null, 3, 'Intento de reseteo de contraseña fallido - usuario no encontrado', 'Error');
                throw ValidationException::withMessages([
                    'email' => [__('No se pudo encontrar un usuario con ese correo electrónico.')],
                ]);
            }

            if (empty($user->two_factor_secret) || 
                !$this->provider->verify(decrypt($user->two_factor_secret), $request->input('code'))) {
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

        // Verificar que existan correos antes de enviar
        if (count($adminEmails) > 0) {
            // Enviar el correo a los administradores
            Mail::to($adminEmails)->send(new PasswordResetNotification($user));
        }

        $this->registrarEnBitacora($user->Id_usuario, 3, 'Usuario desbloqueado correctamente y contraseña restablecida', 'Update');

            // Redirigir a la misma vista o a donde sea necesario
            return redirect()->route('login')->with('status', __('Contraseña restablecida correctamente. Inicie sesión con su nueva contraseña.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->registrarEnBitacora($user->Id_usuario, 3, 'Error desconocido al intentar restablecer la contraseña', 'Error');
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
    // Validar los datos del formulario
    $request->validate([
        'Correo_Electronico' => 'required|email',
        'Contrasena' => 'required|confirmed|min:8',
        'token' => 'required',
    ]);

    // Verificar si el token es válido y pertenece al correo ingresado
    $reset = DB::table('password_reset_tokens')
                ->where('email', $request->Correo_Electronico)
                ->where('token', $request->token)
                ->first();

    if (!$reset) {
        return redirect()->back()->withErrors(['Correo_Electronico' => 'El correo ingresado es incorrecto.']);
    }

    // Buscar al usuario por 'Correo_Electronico'
    $user = User::where('Correo_Electronico', $request->Correo_Electronico)->first();

    if (!$user) {
        return redirect()->back()->withErrors(['Correo_Electronico' => 'No existe un usuario con este correo.']);
    }

    // Actualizar la contraseña del usuario
    $user->Contrasena = bcrypt($request->Contrasena);
    $user->save();

    // Eliminar el token de la tabla password_resets
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

