<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        return $this->markdown('emails.reset_password')
                    ->with([
                        'url' => route('password.reset', ['token' => $this->token]),
                        'usuario' => $this->user->name,
                    ]);
    }
}
