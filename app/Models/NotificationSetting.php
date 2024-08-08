<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'email_notification_activity_in_workspace',
        'email_notification_always_send_email_notifications',
        'email_notification_email_digest',
        'email_notification_announcement_and_update_emails',
        'slack_notifications_activity_on_your_workspace',
        'slack_notifications_always_send_email_notifications',
        'slack_notifications_announcement_and_update_emails',
        'mobile_push_notifications'
    ];

}
