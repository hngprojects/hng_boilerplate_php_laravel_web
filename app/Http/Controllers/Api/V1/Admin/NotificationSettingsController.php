<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateNotificationSettingsRequest;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class NotificationSettingsController extends Controller
{
    /**
     * Update the user's notification settings.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'email_notification_activity_in_workspace' => 'required|boolean',
            'email_notification_always_send_email_notifications' => 'required|boolean',
            'email_notification_email_digest' => 'required|boolean',
            'email_notification_announcement_and_update_emails' => 'required|boolean',
            'slack_notifications_activity_on_your_workspace' => 'required|boolean',
            'slack_notifications_always_send_email_notifications' => 'required|boolean',
            'slack_notifications_announcement_and_update_emails' => 'required|boolean',
            'mobile_push_notifications' => 'required|boolean',
        ]);

        // Get authenticated user
        $user = $request->user();

        // Update or create notification settings
        $settings = NotificationSetting::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        // Send notification about updated settings
        NotificationService::sendEmail($user, 'Notification Settings Updated', 'Your notification settings have been updated.');

        return response()->json([
            'message' => 'Notification settings updated successfully.',
            'data' => $settings
        ]);
    }

    /**
     * Display the user's notification settings.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        // Get authenticated user
        $user = $request->user();

        // Retrieve user's notification settings
        $settings = NotificationSetting::where('user_id', $user->id)->firstOrFail();

        return response()->json($settings);
    }
}
