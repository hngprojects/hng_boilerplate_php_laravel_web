<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationSettingsUpdated;

class NotificationService
{
    public static function sendEmail($user, $subject, $message)
    {
        Mail::to($user->email)->send(new NotificationSettingsUpdated($subject, $message));
    }
}
