<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterSubscriptionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope()
    {
        return [
            'subject' => 'Welcome to Our Newsletter!',
        ];
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->view('emails.newsletter_subscription')
                    ->with([
                        'user' => $this->user,
                    ]);
    }
}
