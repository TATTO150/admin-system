<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ThrottleLogins
{
    public function handle($request, Closure $next)
    {
        $username = $request->input('email');
        $key = 'login_attempts_' . $username;

        // Revisa si el usuario ha sido bloqueado
        $user = \App\Models\User::where('email', $username)->first();
        if ($user && $user->Estado_Usuario === 'Bloqueado') {
            return redirect()->route('bloqueo');
        }

        if (Cache::has($key)) {
            $attempts = Cache::get($key);
            if ($attempts >= 3) {
                if ($user) {
                    $user->Estado_Usuario = 'Bloqueado';
                    $user->save();
                }
                return redirect()->route('bloqueo');
            }
        }

        return $next($request);
    }

    public function terminate($request, $response)
    {
        $username = $request->input('email');
        $key = 'login_attempts_' . $username;

        if ($response->status() == 401) { // 401 means unauthorized
            $attempts = Cache::increment($key);
            Cache::put($key, $attempts, Carbon::now()->addMinutes(15)); // You can adjust the expiration time
        }
    }
}
