<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import the User model

class NotificationSettingController extends Controller
{
    public function update(Request $request, $user_id)
    {
        // Ensure the authenticated user is the owner of the settings being updated
        if (Auth::id() != $user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'email_notifications' => 'required|boolean',
            'sms_notifications' => 'required|boolean',
        ]);

        $user = User::findOrFail($user_id); // Fetch the user using the user_id from the URL

        $notificationSetting = $user->notificationSetting()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'email_notifications' => $request->email_notifications,
                'sms_notifications' => $request->sms_notifications,
            ]
        );

        return response()->json(['message' => 'Notification settings updated successfully', 'data' => $notificationSetting], 200);
    }
}
