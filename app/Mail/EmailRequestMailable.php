<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailRequestMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $emailRequest;

    /**
     * Create a new message instance.
     */
    public function __construct($emailRequest)
    {
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

        // Replace variables in content
        $content = $this->replaceVariables($template->content, $this->emailRequest->variables);

        return $this->html($content)
                    ->subject($template->subject);
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
