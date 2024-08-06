<?php

namespace App\Http\Controllers;

use App\Models\NotificationSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class NotificationSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user = \request()->user();
        return response()->json([
            'status' => 'success',
            'message' => 'Notification preferences retrieved successfully',
            'status_code' => 200,
            'data' => $user->notificationSetting
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NotificationSetting $notificationSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'mobile_push_notifications' => 'required|boolean',
            'email_notification_activity_in_workspace' => 'required|boolean',
            'email_notification_always_send_email_notifications' => 'required|boolean',
            'email_notification_email_digest' => 'required|boolean',
            'email_notification_announcement_and_update_emails' => 'required|boolean',
            'slack_notifications_activity_on_your_workspace' => 'required|boolean',
            'slack_notifications_always_send_email_notifications' => 'required|boolean',
            'slack_notifications_announcement_and_update_emails' => 'required|boolean',
        ]);
        $user = Auth::user();
        $settings = $user->notificationSetting;
        $settings->update($request->all());
        return response()->json([
            'status' => 'success',
            'message' => 'Notification preferences updated successfully',
            'status_code' => 200,
            'data' => $settings
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NotificationSetting $notificationSetting)
    {
        //
    }
}
