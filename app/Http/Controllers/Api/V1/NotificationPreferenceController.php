<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use App\Models\SubscriptionPlan;
use App\Mail\SubscriptionSuccessful;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class NotificationPreferenceController extends Controller
{
    // Existing update method
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

    // New method to handle subscriptions
    public function subscribe(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'user_id' => 'required|uuid',
            'plan_id' => 'required|uuid|exists:subscription_plans,id',
        ]);

        // Validate that the user exists
        $user = User::find($validatedData['user_id']);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
                'status_code' => 404,
            ], 404);
        }

        // Check if the authenticated user is the owner of the subscription or has appropriate permissions
        if (Auth::id() != $validatedData['user_id']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'status_code' => 401,
            ], 401);
        }

        // Fetch the subscription plan
        $subscriptionPlan = SubscriptionPlan::findOrFail($validatedData['plan_id']);

        // Fetch the user's notification preferences
        $notificationPreference = NotificationPreference::where('user_id', $validatedData['user_id'])->first();

        // Send email if email notifications are enabled
        if ($notificationPreference && $notificationPreference->email_notifications) {
            Mail::to($user->email)->send(new SubscriptionSuccessful($subscriptionPlan));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Subscription successful and notification sent!',
            'status_code' => 201,
        ], 201);
    }
}
