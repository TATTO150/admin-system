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

    if ($code = $request->input('code')) {
        if (!$user->two_factor_secret || !$user->validateTwoFactorAuthenticationCode($code)) {
            throw ValidationException::withMessages([
                'code' => [__('El codigo otp ingresado es invalido')],
            ]);
        }
    } elseif ($recovery_code = $request->input('recovery_code')) {
        if (!$user->recoverTwoFactorAuthentication($recovery_code)) {
            throw ValidationException::withMessages([
                'recovery_code' => [__('El codigo otp ingresado es invalido')],
            ]);
        }
    }

    $this->guard->login($user, $request->remember());

    $request->session()->regenerate();

    return redirect()->route('dashboard');
}
}
