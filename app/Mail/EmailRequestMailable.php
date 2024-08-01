<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EmailRequestMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $emailRequest;
    /**
     * Create a new message instance.
     */
    public function __construct($emailRequest)
    {
        // Log::info("Email request $emailRequest");
        $this->emailRequest = $emailRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    
    public function build()
    {
        $template = $this->emailRequest->template;
        return $this->view('emails.dynamic_template')
        ->with(['content' => $this->replaceVariables($template->template, $this->emailRequest->variables)]);
    }

    /**
     * Replace variables in the email content.
     *
     * @param  string  $content
     * @param  array  $variables
     * @return string
     */
    protected function replaceVariables($content, $variables)
    {
        foreach ($variables as $key => $value) {
            $content = str_replace("{{ $key }}", $value, $content);
        }

        return $content;
    }
}
