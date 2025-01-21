<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationLink;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $verificationLink)
    {
        $this->user = $user;
        $this->verificationLink = $verificationLink;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->subject('Verifikacija naloga') 
            ->view('emails.verify-email')  // Blade šablon za email
            ->with([
                'user' => $this->user,
                'link' => $this->verificationLink,
            ]);
    }
}
