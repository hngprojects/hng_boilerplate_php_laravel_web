<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailRequestMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $variables;
    public $htmlContent;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct($variables, $htmlContent, $subject)
    {
        $this->variables = $variables;
        $this->htmlContent = $htmlContent;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Replace placeholders in the email content with variables
        foreach ($this->variables as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            if (strpos($this->htmlContent, $placeholder) !== false) {
                $this->htmlContent = str_replace($placeholder, $value, $this->htmlContent);
            }
        }

        return $this->view('emails.generic') // Assume you have a generic view
                    ->with(['content' => $this->htmlContent])
                    ->subject($this->subject);
    }
}
