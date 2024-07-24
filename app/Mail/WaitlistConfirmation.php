<?php 

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class WaitlistConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $waitlistUser;

    public function __construct($waitlistUser)
    {
        $this->waitlistUser = $waitlistUser;
    }

    public function build()
    {
        return $this->subject('Waitlist Signup Successful')
                    ->view('emails.waitlist_confirmation');
    }
}
