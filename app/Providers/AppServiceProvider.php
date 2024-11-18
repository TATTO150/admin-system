<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
{
    Response::macro('withCsp', function ($response) {
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; frame-src 'none'; frame-ancestors 'none'; connect-src 'self'"
        );
        return $response;
    });

    // Opcional: Si quieres aplicarlo a todas las respuestas
    app('router')->matched(function () {
        app('router')->middleware(function ($request, $next) {
            $response = $next($request);
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; frame-src 'none'; frame-ancestors 'none'; connect-src 'self'"
            );
            return $response;
        });
    });
}

}
