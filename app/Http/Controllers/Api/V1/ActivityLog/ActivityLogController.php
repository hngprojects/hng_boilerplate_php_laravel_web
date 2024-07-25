<?php

namespace App\Http\Controllers\Api\V1\ActivityLog;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityLogController extends Controller
{
    public function getActivityLogs($orgId, $userId)
    {
        // check if the orgid and userid is not in uuid format it should return the 404 error
        $organization = Organisation::find($orgId);
        $targetUser = User::find($userId);
        if (!Str::isUuid($orgId) || !Str::isUuid($userId)) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Organization or user not found',
                'error' => 'Not Found'
            ], 404);
        }
        // Check if the organization and user exist
        if (!$organization || !$targetUser) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Organization or user not found',
                'error' => 'Not Found'
            ], 404);
        }

        $user = auth()->user();

        // Check if the authenticated user belongs to the organization
        if (!$organization->users->contains($user->id)) {
            return response()->json([
                'status_code' => 403,
                'message' => 'Forbidden',
                'error' => 'You do not have permission to view these activity logs'
            ], 403);
        }

        // Retrieve activity logs for the target user
        $activityLogs = ActivityLog::where('user_id', $userId)->get(['activity', 'timestamp']);

        return response()->json([
            'message' => 'Activity logs retrieved successfully',
            'status_code' => 200,
            'data' => $activityLogs
        ], 200);
    }
}
