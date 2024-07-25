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
        if (!Str::isUuid($orgId) || !Str::isUuid($userId)) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Organization or user not found',
                'error' => 'Not Found'
            ], 404);
        }

        $organization = Organisation::find($orgId);
        $target_user = User::find($userId);

        if (!$organization || !$target_user) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Organization or user not found',
                'error' => 'Not Found'
            ], 404);
        }

        $user = auth()->user();

        if (!$organization->users->contains($user->id)) {
            return response()->json([
                'status_code' => 403,
                'message' => 'Forbidden',
                'error' => 'You do not have permission to view these activity logs'
            ], 403);
        }

        $activity_logs = $target_user->activityLogs()->get();

        return response()->json([
            'status_code' => 200,
            'message' => 'Activity logs retrieved successfully',
            'data' => $activity_logs
        ], 200);
    }
}
