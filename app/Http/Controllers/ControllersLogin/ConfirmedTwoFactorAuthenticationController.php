<?php

namespace App\Http\Controllers\ControllersLogin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use App\Models\Bitacora;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ConfirmedTwoFactorAuthenticationController extends Controller
{
    protected $provider;

    public function __construct(TwoFactorAuthenticationProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Enable two factor authentication for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = $request->user();
        try {
            $request->validate([
                'code' => 'required|string',
            ]);

            $user = $request->user();

            if (empty($user->two_factor_secret) || 
                !$this->provider->verify(decrypt($user->two_factor_secret), $request->input('code'))) {
                $this->registrarEnBitacora($user->Id_usuario, 2, 'Intento de verificación de código OTP fallido', 'Error');
                throw ValidationException::withMessages([
                    'code' => 'El código de autenticación proporcionado es incorrecto.'
                ]);
            }else{
                $this->registrarEnBitacora($user->Id_usuario, 2, 'Código OTP verificado correctamente', 'Update');
                return redirect()->route('dashboard');
            }
            
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error during 2FA confirmation', [
                'user_id' => $user->Id_usuario,
                'error' => $e->getMessage()
            ]);

            $this->registrarEnBitacora($user->Id_usuario, 2, 'Error desconocido al intentar verificar el código OTP', 'Error');
            throw ValidationException::withMessages([
                'code' => 'Hubo un error al intentar verificar el código de autenticación.'
            ]);
        }
    }

    /**
     * Registra un evento en la bitácora.
     *
     * @param  int  $Id_usuario
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
