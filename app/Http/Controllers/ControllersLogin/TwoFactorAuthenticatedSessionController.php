<?php

namespace App\Http\Controllers\ControllersLogin;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Controller;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse;
use Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Laravel\Fortify\Events\RecoveryCodeReplaced;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TwoFactorAuthenticatedSessionController extends Controller
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Show the two factor authentication challenge view.
     *
     * @param  \Laravel\Fortify\Http\Requests\TwoFactorLoginRequest  $request
     * @return \Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse
     */
    public function create(TwoFactorLoginRequest $request): TwoFactorChallengeViewResponse
    {
        if (! $request->hasChallengedUser()) {
            throw new HttpResponseException(redirect()->route('login'));
        }

        return app(TwoFactorChallengeViewResponse::class);
    }

    /**
     * Attempt to authenticate a new session using the two factor authentication code.
     *
     * @param  \Laravel\Fortify\Http\Requests\TwoFactorLoginRequest  $request
     * @return mixed
     */
    public function store(Request $request)
{
    $request->validate([
        'code' => 'nullable|string',
        'recovery_code' => 'nullable|string',
    ]);

    $user = $request->challengedUser();

    // Verificar el código OTP
    if ($code = $request->input('code')) {
        if (!$user->two_factor_secret || !$user->validateTwoFactorAuthenticationCode($code)) {
            // Incrementar los intentos
            $user->Intentos_OTP += 1;
            $user->save(); // Guardar los cambios en la base de datos

            // Verificar si los intentos alcanzan el límite
            if ($user->Intentos_OTP >= 3) {
                // Cambiar el estado del usuario a 'Bloqueado'
                $user->Estado_Usuario = 'BLOQUEADO';
                $user->save(); // Guardar el cambio de estado

                // Redirigir a la ruta de bloqueo
                return redirect()->route('bloqueo')->with('error', __('Su cuenta ha sido bloqueada por demasiados intentos fallidos.'));
            }

            throw ValidationException::withMessages([
                'code' => [__('El código OTP ingresado es inválido.')],
            ]);
        }
    } elseif ($recovery_code = $request->input('recovery_code')) {
        if (!$user->recoverTwoFactorAuthentication($recovery_code)) {
            // Incrementar los intentos
            $user->Intentos_OTP += 1;
            $user->save(); // Guardar los cambios en la base de datos

            // Verificar si los intentos alcanzan el límite
            if ($user->Intentos_OTP >= 3) {
                // Cambiar el estado del usuario a 'Bloqueado'
                $user->Estado_Usuario = 'BLOQUEADO';
                $user->save(); // Guardar el cambio de estado

                // Redirigir a la ruta de bloqueo
                return redirect()->route('bloqueo')->with('error', __('Su cuenta ha sido bloqueada por demasiados intentos fallidos.'));
            }

            throw ValidationException::withMessages([
                'recovery_code' => [__('El código de recuperación ingresado es inválido.')],
            ]);
        }
    }

    // Actualizar la verificación del usuario solo si no ha sido verificado previamente
    if ($user->Verificacion_Usuario == 0) {
        $user->Verificacion_Usuario = 1;
        $user->save();
    }

    // Reiniciar intentos de OTP
    $user->Intentos_OTP = 0;
    $user->save();

    // Iniciar sesión
    $this->guard->login($user, $request->remember());

    // Regenerar la sesión
    $request->session()->regenerate();

    return redirect()->route('dashboard');
}

}
