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
            'password' => 'required|string|min:4',
        ]);    

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->errors(),
                'errors' => 'Bad Request'
            ], 400);
        }

        /* $key = 'login_attempts_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => 'Too Many login attempts. Please try again in one hour',
                'error' => 'too_many_attempts',
                'status_code' => 403
            ], 403);
        } */

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            $key = 'login_attempts_'.request()->ip();
            RateLimiter::hit($key,3600);
            return response()->json([
                'status_code' => 401,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // RateLimiter::clear($key);

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
        $profile = $user->profile();

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
                    'role' => $user->role,
                    "avatar_url" => $profile->avatar_url ?? null
                ]
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
