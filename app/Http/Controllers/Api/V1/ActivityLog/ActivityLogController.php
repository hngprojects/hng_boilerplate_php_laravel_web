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
        // Check if orgId and userId are valid UUIDs
        if (!Str::isUuid($orgId) || !Str::isUuid($userId)) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Organization or user not found',
                'error' => 'Not Found'
            ], 404);
        }

        // Find the organization and user
        $organization = Organisation::find($orgId);
        $targetUser = User::find($userId);

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

        $activityLogs = $targetUser->activityLogs()->get();

        // Return the activity logs
        return response()->json([
            'status_code' => 200,
            'message' => 'Activity logs retrieved successfully',
            'data' => $activityLogs
        ], 200);
    }
}
