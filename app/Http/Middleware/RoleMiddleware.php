<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $roleId = DB::table('tbl_ms_usuario')->where('id', $userId)->value('Id_Rol');
            view()->share('userRoleId', $roleId);
        }

        return $next($request);
    }
}
