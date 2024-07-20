<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'user',
            'signup_type' => 'email',
            'is_active' => true,
            'is_verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $name = explode(" ", $user->name);
        $first_name = current($name);
        if (count($name) > 1) {
            $last_name = end($name);
        } else {
            $last_name = "";
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'data' => [
                'accessToken' => $token,
                'user' => [
                    'name' => count($name),
                    'userId' => $user->id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ]
            ]
        ], 201);
    }
}
