<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Invalid credentials',
                'data' => []
            ], 401);
        }

        $user = Auth::user();
        $profile = $user->profile;

        $organisations = $user->organisations->map(function ($org) use ($user) {
            return [
                'organisation_id' => $org->org_id,
                'name' => $org->name,
                'role' => $org->pivot->role,
                'is_owner' => $org->pivot->user_id === $user->id
            ];
        });

        return response()->json([
            'status_code' => 200,
            'message' => 'Login successful',
            'access_token' => $token,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $profile->first_name,
                    'last_name' => $profile->last_name,
                    'email' => $user->email,
                    'avatar_url' => $profile->avatar_url,
                    'is_superadmin' => $user->role === 'superAdmin'
                ],
                'organisations' => $organisations
            ]
        ], 200);
    }


    public function logout()
    {
        try {
            JWTAuth::parseToken()->invalidate(true);
            return response()->json([
                'status_code' => 200,
                'message' => 'Logout successful',
                'data' => []
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status_code' => 401,
                'message' => $e->getMessage(),
                'data' => []
            ], 401);
        }
    }
}
