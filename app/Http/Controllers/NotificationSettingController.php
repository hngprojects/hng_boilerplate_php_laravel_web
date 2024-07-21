<?php

namespace App\Http\Controllers;

use App\Models\NotificationSetting;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'email_notifications' => 'required|boolean',
            'sms_notifications' => 'required|boolean',
        ]);

        $user = auth()->user();
        $notificationSetting = NotificationSetting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'email_notifications' => $request->email_notifications,
                'sms_notifications' => $request->sms_notifications,
            ]
        );

        return response()->json([
            'message' => 'Notification settings updated successfully',
            'data' => $notificationSetting,
        ]);
    }
}
