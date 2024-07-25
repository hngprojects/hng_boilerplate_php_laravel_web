<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);    

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $key = 'login_attempts_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => 'Too Many login attempts. Please try again in one hour',
                'error' => 'too_many_attempts',
                'status_code' => 403
            ], 403);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            $key = 'login_attempts_'.request()->ip();
            RateLimiter::hit($key,3600);
            return response()->json([
                'message' => 'Invalid credentials',
                'error' => 'authentication_failed',
                'status_code' => 401
            ], 401);
        }

        RateLimiter::clear($key);

        $user = Auth::user();
        // $user->last_login_at = now();
        /** @var \App\Models\User $user **/
        $user->save();

        $name_list = explode(" ", $user->name);
        $first_name = current($name_list);
        if (count($name_list) > 1) {
            $last_name = end($name_list);
        } else {
            $last_name = "";
        }

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'signup_type' => $user->signup_type,
                    'is_active' => $user->is_active,
                    'is_verified' => $user->is_verified,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    // 'last_login_at' => $user->last_login_at,
                ],
                'access_token' => $token,
                'refresh_token' => null // JWT does not inherently support refresh tokens; you might need to implement this yourself
            ]
        ], 200);
    }

    public function logout()
    {
        try {
            JWTAuth::parseToken()->invalidate(true);
            return response()->json([
                'message' => 'Logout successful',
                'status_code' => 200
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $this->getErrorCode($e),
                'status_code' => 401
            ], 401);
        }
    }

    private function getErrorCode($exception)
    {
        if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
            return 'token_expired';
        } elseif ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
            return 'token_invalid';
        } else {
            return 'token_absent';
        }
    }
}
