<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MagicLinkEmail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class MagicLinkController extends Controller
{
    
    // Send a magic link to the user's email.
    public function sendMagicLink(Request $request)
    {
        try {
        
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 400,
                    'status' => 'error',
                    'message' => 'Invalid email address',
                    'data' => $validator->errors(),
                ], 400);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status_code' => 404,
                    'status' => 'error',
                    'message' => 'User not found',
                    'data' => [],
                ], 404);
            }

            // Generate a unique token
            $token = Str::random(60);
            $expiresAt = Carbon::now()->addMinutes(30); // expires in 30 minutes

            // Save the token to the user in db
            $user->magic_link_token = $token;
            $user->magic_link_expires_at = $expiresAt;
            $user->save();

            // Send email
            Mail::to($user->email)->send(new MagicLinkEmail($user->email, $token));

            return response()->json([
                'status_code' => 200,
                'status' => 'success',
                'message' => 'Verification token sent to email',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'status' => 'error',
                'message' => 'Failed to send email',
            ], 500);
        }
    }
    // End of method 


    // Verify the magic link token
    public function verifyMagicLink(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'token' => 'required|string',
            ]);
        
            if ($validator->fails()) {
                return response()->json([
                    'status_code' => 401,
                    'message' => 'Invalid request data',
                    'error' => $validator->errors()
                ], 401);
            }
        
            $user = User::where('email', $request->email)->first();
        
            if (!$user) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'User not found',
                ], 404);
            }
        
            // Check if token matches and is not expired
            if (
                $user->magic_link_token !== $request->token ||
                Carbon::parse($user->magic_link_expires_at)->isPast()
            ) {
                return response()->json([
                    'status_code' => 401,
                    'status' => 'error',
                    'message' => 'Invalid or expired token',
                ], 401);
            }
        

            // Do what normal login would perform
            // Generate JWT token
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json([
                    'status_code' => 500,
                    'message' => 'Could not generate access token',
                ], 500);
            }
        
            // Update user status
            $user->is_active = 1;
            $user->last_login_at = now();
            $user->magic_link_token = null;
            $user->magic_link_expires_at = null;
            $user->save();
        
            // Fetch user organizations
            $organisations = $user->owned_organisations->map(function ($org) use ($user) {
                return [
                    'organisation_id' => (string) $org->org_id,
                    'name' => $org->name,
                    'user_role' => $user->roles()->where('org_id', $org->org_id)->first()->name ?? 'user',
                    'is_owner' => true,
                ];
            });
        
            $is_superadmin = in_array($user->role, ['admin']);
        
            return response()->json([
                'status_code' => 200,
                'message' => 'Login successful',
                'access_token' => $token,
                'data' => [
                    'user' => [
                        'id' => (string) $user->id,
                        'first_name' => $user->profile->first_name,
                        'last_name' => $user->profile->last_name,
                        'email' => $user->email,
                        'avatar_url' => $user->profile->avatar_url,
                        'is_superadmin' => $is_superadmin,
                        'role' => $user->role,
                    ],
                    'organisations' => $organisations,
                ]
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'status' => 'error',
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
}