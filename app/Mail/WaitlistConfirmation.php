<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class WaitlistConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $waitlist_user;

    public function __construct($waitlist_user)
    {
        $this->waitlist_user = $waitlist_user;
    }

    public function build()
    {
        return $this->subject('Waitlist Confirmation')
                    ->view('emails.waitlist_confirmation');
    }
}
