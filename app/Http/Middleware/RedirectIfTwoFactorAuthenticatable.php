<?php

// app/Http/Middleware/RedirectIfTwoFactorAuthenticatable.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfTwoFactorAuthenticatable
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (optional($user)->two_factor_secret && ! $request->is('two-factor-challenge')) {
            return redirect()->route('two-factor.login');
        }

        return $next($request);
    }
}
