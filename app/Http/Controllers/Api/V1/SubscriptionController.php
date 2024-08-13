<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\NotificationPreference;
use App\Mail\SubscriptionSuccessful;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|uuid',
            'plan_id' => 'required|uuid|exists:subscription_plans,id',
        ]);

        $subscriptionPlan = SubscriptionPlan::findOrFail($validatedData['plan_id']);

        // Logic to create subscription here (assuming you have a Subscription model)

        // Fetch the user's notification preferences
        $notificationPreference = NotificationPreference::where('user_id', $validatedData['user_id'])->first();

        // Check if email notifications are enabled
        if ($notificationPreference && $notificationPreference->email_notifications) {
            Mail::to($request->user()->email)->send(new SubscriptionSuccessful($subscriptionPlan));
        }

        return response()->json(['message' => 'Subscription successful and notification sent!'], 201);
    }

    private function sendSubscriptionEmail($subscription){
        $user =$subscription->user;
        $notificationSettings = $user->notificationSetting;
        if($notificationSettings->email_notification){
            Mail::to($request->user()->email)->send(new SubscriptionSuccessful($subscription));

        }
    }
}
