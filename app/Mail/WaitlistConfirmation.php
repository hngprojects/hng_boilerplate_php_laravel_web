<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WaitlistConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $waitlist;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($waitlist)
    {
        $this->waitlist = $waitlist;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Waitlist Confirmation')
                    ->view('emails.waitlist_confirmation');
    }
}
