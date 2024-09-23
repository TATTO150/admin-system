<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTwoFactorIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Verificar si el usuario está autenticado y tiene un two_factor_secret configurado
        if ($user && $user->two_factor_secret && !$request->session()->get('two_factor_passed')) {
            // Redirigir al two-factor login si no ha pasado el 2FA
            return redirect()->route('two-factor.login');
        }

        return $next($request);
    }
}
