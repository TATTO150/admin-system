<?php
  
namespace App\Http;
  
use Illuminate\Foundation\Http\Kernel as HttpKernel;
  
class Kernel extends HttpKernel
{
   
    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        '2fa' => \PragmaRX\Google2FALaravel\Middleware::class,
        'throttle.logins' => \App\Http\Middleware\ThrottleLogins::class,
        'two_factor_verified' => \App\Http\Middleware\EnsureTwoFactorIsVerified::class,
        'verificar.sesion.activa' => \App\Http\Middleware\VerificarSesionActiva::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            // Otros middlewares...
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':login',
            \Illuminate\Session\Middleware\StartSession::class,
            \App\Http\Middleware\RoleMiddleware::class,
        ],
        // Otros grupos de middleware...
    ];
    
    
    //ESTO FUE LO QUE LE AÑADI

    protected $middleware = [
        // Otros middlewares
        \App\Http\Middleware\ContentSecurityPolicy::class,
        \App\Http\Middleware\AntiClickjacking::class,
    ];
}