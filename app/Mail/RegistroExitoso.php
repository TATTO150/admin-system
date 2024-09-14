<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistroExitoso extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $contrasena;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($usuario, $contrasena)
    {
        $this->usuario = $usuario;
        $this->contrasena = $contrasena;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.registro-exitoso')
                    ->subject('Registro Exitoso')
                    ->with([
                        'usuario' => $this->usuario,
                        'contrasena' => $this->contrasena,
                    ]);
    }
}
