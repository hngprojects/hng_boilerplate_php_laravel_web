<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\Log;

class InvitationAcceptanceController extends Controller
{
    public function acceptInvitation(Request $request)
    {
        $token = $request->query('token');
        $invitation = Invitation::where('link', $token)->first();

        if (!$invitation) {
            return response()->json([
                'message' => 'Invalid or expired invitation link',
                'errors' => ['Invalid invitation link format', 'Expired invitation link', 'Organization not found'],
                'status_code' => 400
            ], 400);
        }

        if ($invitation->expires_at < now()) {
            return response()->json([
                'message' => 'Invitation link has expired',
                'errors' => ['Expired invitation link'],
                'status_code' => 400
            ], 400);
        }

        $user = User::firstOrCreate(['email' => $invitation->email]);
        $organization = Organization::find($invitation->org_id);

        if (!$organization) {
            return response()->json([
                'message' => 'Organization not found',
                'errors' => ['Organization not found'],
                'status_code' => 400
            ], 400);
        }

        $organization->users()->attach($user->id);

        return response()->json([
            'message' => 'Invitation accepted, you have been added to the organization',
            'status' => 200
        ], 200);
    }

    public function acceptInvitationPost(Request $request)
    {
        $data = $request->validate([
            'invitationLink' => 'required|string'
        ]);

        $invitation = Invitation::where('link', $data['invitationLink'])->first();

        if (!$invitation) {
            return response()->json([
                'message' => 'Invalid or expired invitation link',
                'errors' => ['Invalid invitation link format', 'Expired invitation link', 'Organization not found'],
                'status_code' => 400
            ], 400);
        }

        if ($invitation->expires_at < now()) {
            return response()->json([
                'message' => 'Invitation link has expired',
                'errors' => ['Expired invitation link'],
                'status_code' => 400
            ], 400);
        }

        $user = User::firstOrCreate(['email' => $invitation->email]);
        $organization = Organization::find($invitation->org_id);

        if (!$organization) {
            return response()->json([
                'message' => 'Organization not found',
                'errors' => ['Organization not found'],
                'status_code' => 400
            ], 400);
        }

        $organization->users()->attach($user->id);

        return response()->json([
            'message' => 'Invitation accepted, you have been added to the organization',
            'status' => 200
        ], 200);
    }
}
