<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Rules\Validaciones;
use Carbon\Carbon;

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
                'password' => [(new Validaciones)->requerirSinEspacios()->requerirSimbolo()->requerirMinuscula()->requerirMayuscula()->requerirNumero()->requerirlongitudMinima(8)->requerirlongitudMaxima(12)->requerirCampo()],
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

            // Cambiar el Estado_Usuario dependiendo del Id_Rol
            if ($user->Id_Rol == 3) {
                $user->Estado_Usuario = 'NUEVO';
            } else {
                $user->Estado_Usuario = 'ACTIVO';
            }

            $user->Intentos_Login = 0;
            $user->save();
            $this->registrarEnBitacora($user->Id_usuario, 3, 'Usuario desbloqueado correctamente', 'Update');
                

            $this->registrarEnBitacora($user->Id_usuario, 3, 'Contraseña restablecida correctamente', 'Actualización');

            // Redirigir a la misma vista o a donde sea necesario
            return redirect()->route('login')->with('status', __('Contraseña restablecida correctamente. Inicie sesión con su nueva contraseña.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->registrarEnBitacora(null, 3, 'Error desconocido al intentar restablecer la contraseña', 'Error');
            throw ValidationException::withMessages([
                'error' => [__('Hubo un error al intentar restablecer la contraseña.')],
            ]);
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

