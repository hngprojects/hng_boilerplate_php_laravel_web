<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Organisation;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InvitationAcceptanceController extends Controller
{
    /**
     * Generate and store an invitation.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateInvitation(Request $request)
    {
        $data = $request->validate([
            'org_id' => 'required|exists:organisations,org_id', // Correct table name
            'expires_at' => 'required|date|after:now'
        ]);

        $token = Str::random(32); // Generate a unique token

        $invitation = Invitation::create([
            'uuid' => (string) Str::uuid(),
            'org_id' => $data['org_id'],
            'link' => $token,
            'expires_at' => Carbon::parse($data['expires_at']),
        ]);

        return response()->json([
            'invitation' => $invitation,
            'message' => 'Invitation created successfully',
            'status' => 201
        ], 201);
    }

    /**
     * Accept an invitation via GET request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptInvitation(Request $request)
    {
        $token = $request->query('token');
        $invitation = Invitation::where('link', $token)->valid()->first();

        if (!$invitation) {
            return response()->json([
                'message' => 'Invalid or expired invitation link',
                'errors' => ['Invalid invitation link format', 'Expired invitation link', 'Organization not found'],
                'status_code' => 400
            ], 400);
        }

        // Find or create user by email (assuming email is included in the Invitation model)
        $user = User::firstOrCreate(['email' => $invitation->email]);
        $organization = $invitation->organization;

        if (!$organization) {
            return response()->json([
                'message' => 'Organization not found',
                'errors' => ['Organization not found'],
                'status_code' => 400
            ], 400);
        }

        // Attach the user to the organization
        $organization->users()->attach($user->id);

        return response()->json([
            'message' => 'Invitation accepted, you have been added to the organization',
            'status' => 200
        ], 200);
    }

    /**
     * Accept an invitation via POST request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptInvitationPost(Request $request)
    {
        $data = $request->validate([
            'invitationLink' => 'required|string'
        ]);

        $invitation = Invitation::where('link', $data['invitationLink'])->valid()->first();

        if (!$invitation) {
            return response()->json([
                'message' => 'Invalid or expired invitation link',
                'errors' => ['Invalid invitation link format', 'Expired invitation link', 'Organization not found'],
                'status_code' => 400
            ], 400);
        }

        // Find or create user by email (assuming email is included in the Invitation model)
        $user = User::firstOrCreate(['email' => $invitation->email]);
        $organization = $invitation->organization;

        if (!$organization) {
            return response()->json([
                'message' => 'Organization not found',
                'errors' => ['Organization not found'],
                'status_code' => 400
            ], 400);
        }

        // Attach the user to the organization
        $organization->users()->attach($user->id);

        return response()->json([
            'message' => 'Invitation accepted, you have been added to the organization',
            'status' => 200
        ], 200);
    }
}
