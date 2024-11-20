<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XFrameOptionsMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('X-Frame-Options', 'DENY'); // Usa SAMEORIGIN si permites iframes en tu dominio
        return $response;
    }
}
