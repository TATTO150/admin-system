<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon; // Importar Carbon correctamente
use Illuminate\Support\Facades\Config; // Importar Config correctamente
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;

class VerifyEmail extends Notification
{
    public static $toMailCallback;
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
                    ->subject('Verifica tu dirección de correo electrónico')
                    ->line('Por favor haz clic en el botón de abajo para verificar tu dirección de correo electrónico.')
                    ->action('Verificar correo electrónico', $verificationUrl)
                    ->line('Si no creaste una cuenta, no se requiere ninguna otra acción.');
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify', 
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)), 
            [
                'id' => $notifiable->getKey(), 
                'hash' => sha1($notifiable->getEmailForVerification())
            ]
        );
    }
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
