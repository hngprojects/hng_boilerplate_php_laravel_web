<?php

namespace App\Mail;

use App\Models\SubscriptionPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionSuccessful extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriptionPlan;

    public function __construct(SubscriptionPlan $subscriptionPlan)
    {
        $this->subscriptionPlan = $subscriptionPlan;
    }

    public function build()
    {
        return $this->subject('Subscription Successful')
                    ->view('emails.subscription_successful');
    }
}
