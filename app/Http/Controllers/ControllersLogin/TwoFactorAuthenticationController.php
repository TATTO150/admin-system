<?php

namespace App\Http\Controllers\ControllersLogin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Contracts\TwoFactorDisabledResponse;
use Laravel\Fortify\Contracts\TwoFactorEnabledResponse;
use App\Models\Bitacora;
use Carbon\Carbon;

class TwoFactorAuthenticationController extends Controller
{
    /**
     * Enable two factor authentication for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Fortify\Actions\EnableTwoFactorAuthentication  $enable
     * @return \Laravel\Fortify\Contracts\TwoFactorEnabledResponse
     */
    public function store(Request $request, EnableTwoFactorAuthentication $enable)
    {
        $enable($request->user(), $request->boolean('force', false));
        
        // Registrar en bitácora
        $this->registrarEnBitacora($request->user(), 2, 'Autenticación en dos factores habilitada', 'Update'); 
        $this->registrarEnBitacora($request->user(), 2, 'Usuario verificado correctamente', 'Update');

        return app(TwoFactorEnabledResponse::class);
    }

    /**
     * Disable two factor authentication for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Fortify\Actions\DisableTwoFactorAuthentication  $disable
     * @return \Laravel\Fortify\Contracts\TwoFactorDisabledResponse
     */
    public function destroy(Request $request, DisableTwoFactorAuthentication $disable)
    {
        $disable($request->user());

        // Registrar en bitácora
        $this->registrarEnBitacora($request->user(), 2, 'Autenticación en dos factores deshabilitada', 'Update'); // ID_objetos 2: 'autenticación en dos factores'

        return app(TwoFactorDisabledResponse::class);
    }

    /**
     * Registra un evento en la bitácora.
     *
     * @param  \App\Models\User  $user
     * @param  int  $ID_objetos
     * @param  string  $descripcion
     * @param  string  $accion
     * @return void
     */
    protected function registrarEnBitacora($user, $ID_objetos, $descripcion, $accion)
    {
        Bitacora::create([
            'Id_usuario' => $user->Id_usuario,
            'ID_objetos' => $ID_objetos,
            'Descripcion' => $descripcion,
            'Fecha' => Carbon::now(),
            'Accion' => $accion
        ]);
    }
}
