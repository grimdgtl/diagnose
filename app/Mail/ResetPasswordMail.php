<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;
    public $actionUrl;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
        $this->actionUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->email,
        ], false));
    }

    public function build()
    {
        return $this->subject('Resetovanje lozinke - Dijagnoza')
                    ->view('emails.reset-password-email')
                    ->with([
                        'actionUrl' => $this->actionUrl,
                    ]);
    }
}