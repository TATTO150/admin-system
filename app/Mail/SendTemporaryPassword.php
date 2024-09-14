<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendTemporaryPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $details; // Almacena los detalles del correo, como el nombre y la contraseña temporal

    /**
     * Crear una nueva instancia del mensaje.
     *
     * @param array $details Detalles del correo (nombre y contraseña temporal)
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details; // Asigna los detalles del correo a la propiedad $details
    }

    /**
     * Construir el mensaje.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Tu contraseña temporal de acceso') // Asigna el asunto del correo
                    ->markdown('emails.temporaryPassword'); // Asigna la vista del correo en formato Markdown
    }
}
