<?php

namespace App\Http\Controllers\ControllersLogin;

use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\RequestPasswordResetLinkViewResponse;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse;
use Laravel\Fortify\Fortify;
use App\Models\Bitacora;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the reset password link request view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\RequestPasswordResetLinkViewResponse
     */
    public function create(Request $request): RequestPasswordResetLinkViewResponse
    {
        return app(RequestPasswordResetLinkViewResponse::class);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function store(Request $request): Responsable
    {
        $request->validate([Fortify::email() => 'required|email']);

        $email = $request->input(Fortify::email());
        Log::info('Email de restablecimiento solicitado para: ' . $email);

        $status = $this->broker()->sendResetLink(
            $request->only(Fortify::email())
        );

        Log::info('Estado del envío del enlace de restablecimiento: ' . $status);

        $user = User::where('Correo_Electronico', $email)->first();

        if ($user) {
            $accion = $status == Password::RESET_LINK_SENT ? 'Solicitud de restablecimiento de contraseña enviada' : 'Error en el envío del enlace de restablecimiento de contraseña';
            $this->registrarEnBitacora($user, 8, $accion, 'Consulta'); // ID_objetos 8: 'contrasena olvidada'
        }

        return $status == Password::RESET_LINK_SENT
            ? app(SuccessfulPasswordResetLinkRequestResponse::class, ['status' => $status])
            : app(FailedPasswordResetLinkRequestResponse::class, ['status' => $status]);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected function broker(): PasswordBroker
    {
        return Password::broker(config('fortify.passwords'));
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
