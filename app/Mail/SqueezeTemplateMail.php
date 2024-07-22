<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Squeeze;

class SqueezeTemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $squeeze;

    public function __construct(Squeeze $squeeze)
    {
        $this->squeeze = $squeeze;
    }

    public function build()
    {
        return $this->view('emails.squeeze_template')
            ->with([
                'first_name' => $this->squeeze->first_name,
            ]);
    }
}
