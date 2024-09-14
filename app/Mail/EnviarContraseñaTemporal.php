<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnviarContraseñaTemporal extends Mailable
{
    use Queueable, SerializesModels;

    public $detalles;

    /**
     * Crear una nueva instancia del mensaje.
     *
     * @return void
     */
    public function __construct($detalles)
    {
        $this->detalles = $detalles;
    }

    /**
     * Construir el mensaje.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Tu contraseña temporal')
                    ->markdown('emails.enviarContraseñaTemporal');
    }
}
