<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
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
            'password' => 'required|string|min:4',
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

        // RateLimiter::clear($key);

        $user = Auth::user();
        $user->last_login_at = now();
        $user->save();

        return response()->json([
            'status_code' => 200,
            'message' => 'Login successful',
            'access_token' => $token,
            'data' => [
                'user' => new UserResource($user->load('profile', 'owned_organisations'))
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
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status_code' => 401,
                'message' => $e->getMessage(),
                'error' => $this->getErrorCode($e)
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
