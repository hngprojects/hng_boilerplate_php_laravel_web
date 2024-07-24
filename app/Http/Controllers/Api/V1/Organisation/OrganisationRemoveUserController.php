<?php

namespace App\Http\Controllers\Api\V1\Organisation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrganisationRemoveUserController extends Controller
{

    public function removeUser(Request $request, $org_id, $user_id)
    {
        $organization = Organisation::findOrFail($org_id);

        // Use $request->auth instead of Auth::user()
        if (!$request->user()->can('removeUser', $organization)) {
            return response()->json([
                'status' => 'Forbidden',
                'message' => 'Only admin can remove users',
                'status_code' => 403
            ], 403);
        }

        $user = User::find($user_id);

        if (!$user || !$organization->users()->detach($user)) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'user not found',
                'status_code' => 404
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'user deleted successfully',
            'status_code' => 200
        ], 200);
    }
}
