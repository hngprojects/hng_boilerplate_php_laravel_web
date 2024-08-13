<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationSettingsRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email_notification_activity_in_workspace' => 'required|boolean',
            'email_notification_always_send_email_notifications' => 'required|boolean',
            'email_notification_email_digest' => 'required|boolean',
            'email_notification_announcement_and_update_emails' => 'required|boolean',
            'slack_notifications_activity_on_your_workspace' => 'required|boolean',
            'slack_notifications_always_send_email_notifications' => 'required|boolean',
            'slack_notifications_announcement_and_update_emails' => 'required|boolean',
            'mobile_push_notifications' => 'required|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'email_notification_activity_in_workspace' => filter_var($this->input('email_notification_activity_in_workspace'), FILTER_VALIDATE_BOOLEAN),
            'email_notification_always_send_email_notifications' => filter_var($this->input('email_notification_always_send_email_notifications'), FILTER_VALIDATE_BOOLEAN),
            'email_notification_email_digest' => filter_var($this->input('email_notification_email_digest'), FILTER_VALIDATE_BOOLEAN),
            'email_notification_announcement_and_update_emails' => filter_var($this->input('email_notification_announcement_and_update_emails'), FILTER_VALIDATE_BOOLEAN),
            'slack_notifications_activity_on_your_workspace' => filter_var($this->input('slack_notifications_activity_on_your_workspace'), FILTER_VALIDATE_BOOLEAN),
            'slack_notifications_always_send_email_notifications' => filter_var($this->input('slack_notifications_always_send_email_notifications'), FILTER_VALIDATE_BOOLEAN),
            'slack_notifications_announcement_and_update_emails' => filter_var($this->input('slack_notifications_announcement_and_update_emails'), FILTER_VALIDATE_BOOLEAN),
            'mobile_push_notifications' => filter_var($this->input('mobile_push_notifications'), FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
