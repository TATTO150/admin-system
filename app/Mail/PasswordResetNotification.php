<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.notificación-restablecimiento-contraseña')
        ->subject('Notificación de Restablecimiento de Contraseña')
        ->with([
            'usuario' => $this->user->Correo_Electronico,
            'nombre' => $this->user->Nombre_Usuario,
            'userId' => $this->user->Id_usuario,
        ]);
    }

}
