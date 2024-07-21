<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
                'error' => 'authentication_failed',
                'statusCode' => 401
            ], 401);
        }

        $user = Auth::user();
        $user->last_login_at = now();

        /** @var \App\Models\User $user **/
        $user->save();

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'signup_type' => $user->signup_type,
                    'is_active' => $user->is_active,
                    'is_verified' => $user->is_verified,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'last_login_at' => $user->last_login_at,
                ],
                'access_token' => $token,
                'refresh_token' => null // JWT does not inherently support refresh tokens; you might need to implement this yourself
            ]
        ], 200);
    }

    // Profile API (GET)
    // public function profile(){
    //     $user_data = auth()->user();

    //     return response()->json([
    //         "status" => true,
    //         "message" => "Profile Data",
    //         "user" => $user_data
    //     ]);
    // }

    // Refresh Token API (GET)
    public function refreshToken(){
        /** @var Illuminate\Auth\AuthManager */
        $auth = auth();

        return response()->json([
            "status" => true,
            "message" => "New Access token generated",
            "new_token" => $auth->refresh()
        ]);
    }

    // Logout API (GET)
    public function logout(){
        auth()->logout();

        return response()->json([
            "status" => true,
            "message" => "User logged out successfully",
        ]);
    }
}



