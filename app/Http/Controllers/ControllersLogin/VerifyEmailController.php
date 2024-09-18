<?php

namespace App\Http\Controllers\ControllersLogin;

use Illuminate\Auth\Events\Verified;
use Illuminate\Routing\Controller;
use Laravel\Fortify\Contracts\VerifyEmailResponse;
use Laravel\Fortify\Http\Requests\VerifyEmailRequest;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Laravel\Fortify\Http\Requests\VerifyEmailRequest  $request
     * @return \Laravel\Fortify\Contracts\VerifyEmailResponse
     */
    public function __invoke(VerifyEmailRequest $request, $Id_usuario)
    {
        $user = User::findOrFail($Id_usuario);
        $hash = sha1($user->Correo_Electronico);

        // Generar la URL firmada para la verificación de correo electrónico
        $url = route('verification.verify', ['Id_usuario' => $user->Id_usuario, 'hash' => $hash]);

        if ($user->hasVerifiedEmail()) {
            return app(VerifyEmailResponse::class);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return app(VerifyEmailResponse::class);
    }
}