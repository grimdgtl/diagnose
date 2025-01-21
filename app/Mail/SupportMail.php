// app/Mail/SupportMail.php

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $supportData;

    /**
     * Create a new message instance.
     */
    public function __construct($supportData)
    {
        $this->supportData = $supportData;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Podrška: ' . $this->supportData['subject'])
                    ->view('emails.support');
    }
}
