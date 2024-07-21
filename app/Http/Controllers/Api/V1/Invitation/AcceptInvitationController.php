<?php

namespace App\Http\Controllers\Api\V1\Invitation;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AcceptInvitationController extends Controller
{
    public function acceptInvitation(Request $request)
    {
        // Validate the request payload
        $validator = Validator::make($request->all(), [
            'invitationLink' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid request data',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        // Retrieve the invitation link from the request
        $invitationLink = $request->input('invitationLink');

        // Find the invitation by the link
        $invitation = Invitation::where('link', $invitationLink)->first();

        if (!$invitation) {
            return response()->json([
                'message' => 'Invalid or expired invitation link',
                'errors' => [' "Invalid invitation link format"'],
                'status_code' => 400
            ], 400);
        }

        // Check if the invitation has expired
        if ($invitation->expires_at < now()) {
            return response()->json([
                'message' => 'Invalid or expired invitation link',
                'errors' => ['Expired invitation link'],
                'status_code' => 400
            ], 400);
        }

        // Create the user with the email from the invitation
        $user = User::where('email', $invitation->email)->first();

        // Add the user to the organization
        $organisation = Organisation::find($invitation->org_id);

        if (!$organisation) {
            return response()->json([
                'message' => 'Invalid or expired invitation link',
                'errors' => ['Organization not found'],
                'status_code' => 400
            ], 400);
        }

        $organisation->users()->attach($user);

        // Delete the invitation to prevent reuse
        $invitation->delete();

        return response()->json([
            'message' => 'Invitation accepted, you have been added to the organization',
            'status_code' => 200
        ], 200);
    }
}
