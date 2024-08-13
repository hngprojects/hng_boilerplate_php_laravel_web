<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;


class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Validation failed',
                'data' => $validator->errors()
            ], 400);
        }

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
                'role' => $org->pivot->role ?? null,
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
                    'first_name' => $profile->first_name ?? null,
                    'last_name' => $profile->last_name ?? null,
                    'email' => $user->email,
                    'avatar_url' => $profile->avatar_url ?? null,
                    'is_superadmin' => $user->role === 'superadmin',
                    'role' => $user->role
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
                'error' => null,
                'data' => []
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Token has expired',
                'error' => 'token_expired',
                'data' => []
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Token is invalid',
                'error' => 'token_invalid',
                'data' => []
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Token is missing',
                'error' => 'token_absent',
                'data' => []
            ], 401);
        }
    }
    
}
