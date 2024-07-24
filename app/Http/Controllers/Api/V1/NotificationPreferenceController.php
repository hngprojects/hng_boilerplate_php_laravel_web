<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationPreferenceController extends Controller
{
    public function update(Request $request, $user_id)
    {

        // Validate that the user exists
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
                'status_code' => 404,
            ], 404);
        }

        // Check if the authenticated user is the owner of the settings or has appropriate permissions
        if (Auth::id() != $user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'status_code' => 401,
            ], 401);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid data', 
                'status_code' => 400,
            ], 400);
        }

        // Find or create the user's notification settings
        $notificationSetting = NotificationPreference::firstOrCreate(
            ['user_id' => $user_id],
            ['email_notifications' => true, 'push_notifications' => true, 'sms_notifications' => true]
        );

        // Update the notification settings
        $notificationSetting->update($request->only(['email_notifications', 'push_notifications', 'sms_notifications']));

        return response()->json([
            'status' => 'success',
            'message' => 'Notification settings updated successfully',
            'status_code' => 200,
        ], 200);
    }
}
