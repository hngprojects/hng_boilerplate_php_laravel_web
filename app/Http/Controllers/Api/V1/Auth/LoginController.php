<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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
                'status_code' => 401,
                'message' => 'Invalid Credentials',
                'error' => 'Invalid Email or Password'
            ], 401);
        }

        if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
            $key = 'login_attempts_'.request()->ip();
            RateLimiter::hit($key,3600);
            return response()->json([
                'status_code' => 401,
                'message' => 'Invalid credentials',
                'error' => 'Invalid Email or Password'
            ], 401);
        }

        $user = Auth::user();
        $user->last_login_at = now();
        $user->save();

        $organisations = $user->owned_organisations->map(function ($org) use ($user) {
            return [
                'organisation_id' => $org->org_id,
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
                    'id' => $user->id,
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
