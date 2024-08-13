<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationSettingsUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;

    public function __construct($subject, $message)
    {
        $this->subject = $subject;
        $this->message = $message;
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.notification_settings_updated')
                    ->with('message', $this->message);
    }
}
