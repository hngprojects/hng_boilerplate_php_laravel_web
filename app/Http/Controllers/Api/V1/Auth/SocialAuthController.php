<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Create or update the user
        $user = User::updateOrCreate(
            ['email' => $googleUser->email],
            [
                'password' => Hash::make(Str::password(12)), // Use a random password
                'social_id' => $googleUser->id, // Save Google ID for future reference
                'is_verified' => true, // Assuming verified on successful social login
                'signup_type' => 'Google',
                'is_active' => true,
            ]
        );

        // dd($user->profile);
        // If profile is a separate model, ensure it is created or updated accordingly
        if ($user->profile) {
            $user->profile->update([
                'first_name' => $googleUser->user['given_name'],
                'last_name' => $googleUser->user['family_name'],
                'avatar_url' => $googleUser->attributes['avatar_original'],
            ]);
        } else {
            // Create a profile if it does not exist
            $user->profile()->create([
                'first_name' => $googleUser->user['given_name'],
                'last_name' => $googleUser->user['family_name'],
                'avatar_url' => $googleUser->attributes['avatar_original'],
            ]);
        }

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        // Prepare the response data
        $response = [
            'status' => 'success',
            'message' => 'User successfully authenticated',
            'access_token' => $token,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $googleUser->user['given_name'],
                'last_name' => $googleUser->user['family_name']
            ]
        ];

        return response()->json($response);
    }

    public function loginUsingFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    public function callbackFromFacebook()
    {
        $facebookUser = Socialite::driver('facebook')->stateless()->user();

        // Create or update the user
        // $user = User::updateOrCreate(
        //     ['email' => $googleUser->email],
        //     [
        //         'password' => Hash::make(Str::password(12)), // Use a random password
        //         'social_id' => $googleUser->id, // Save Google ID for future reference
        //         'is_verified' => true, // Assuming verified on successful social login
        //         'signup_type' => 'Google',
        //         'is_active' => true,
        //     ]
        // );

        // dd($user->profile);
        // If profile is a separate model, ensure it is created or updated accordingly
        // if ($user->profile) {
        //     $user->profile->update([
        //         'first_name' => $googleUser->user['given_name'],
        //         'last_name' => $googleUser->user['family_name'],
        //         'avatar_url' => $googleUser->attributes['avatar_original'],
        //     ]);
        // } else {
        //     // Create a profile if it does not exist
        //     $user->profile()->create([
        //         'first_name' => $googleUser->user['given_name'],
        //         'last_name' => $googleUser->user['family_name'],
        //         'avatar_url' => $googleUser->attributes['avatar_original'],
        //     ]);
        // }

        // Generate JWT token
        // $token = JWTAuth::fromUser($user);

        // Prepare the response data
        // $response = [
        //     'status' => 'success',
        //     'message' => 'User successfully authenticated',
        //     'access_token' => $token,
        //     'data' => [
        //         'id' => $user->id,
        //         'email' => $user->email,
        //         'first_name' => $googleUser->user['given_name'],
        //         'last_name' => $googleUser->user['family_name']
        //     ]
        // ];

        return response()->json($facebookUser);
    }
}
