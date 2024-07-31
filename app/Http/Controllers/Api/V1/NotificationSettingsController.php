<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationSettingsController extends controller
{
    //Implementation of the getSettings and updateSettings methods.


    public function getSettings()
    {
        // Assume the NotificationSettings model is correctly set up

        $user = Auth::user();
        $settings = $user->notificationSettings;

        return response()->json([
            'status' => 'success',
            'message' => 'Notification preferences retrieved successfully',
            'status_code' => 200,
            'data' => $settings
        ], 200);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'mobile_push_notifications' => 'boolean',
            'email_notification_activity_in_workspace' => 'boolean',
            'email_notification_always_send_email_notifications' => 'boolean',
            'email_notification_email_digest' => 'boolean',
            'email_notification_announcement_and_update_emails' => 'boolean',
            'slack_notifications_activity_on_your_workspace' => 'boolean',
            'slack_notifications_always_send_email_notifications' => 'boolean',
            'slack_notifications_announcement_and_update_emails' => 'boolean',
        ]);

        $user = Auth::user();
        $settings = $user->notificationSettings;

        $settings->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Notification preferences updated successfully',
            'status_code' => 200,
            'data' => $settings
        ], 200);
    }
}
