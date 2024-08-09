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
use Illuminate\Support\Facades\Validator;
use Google_Client;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->email],
            [
                'password' => Hash::make(Str::password(12)),
                'social_id' => $googleUser->id,
                'is_verified' => true,
                'signup_type' => 'Google',
                'is_active' => true,
            ]
        );

        if ($user->profile) {
            $user->profile->update([
                'first_name' => $googleUser->user['given_name'],
                'last_name' => $googleUser->user['family_name'],
                'avatar_url' => $googleUser->attributes['avatar_original'],
            ]);
        } else {
            $user->profile()->create([
                'first_name' => $googleUser->user['given_name'],
                'last_name' => $googleUser->user['family_name'],
                'avatar_url' => $googleUser->attributes['avatar_original'],
            ]);
        }

        $token = JWTAuth::fromUser($user);

        $response = [
            'status_code' => 200,
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

    public function saveGoogleRequest(Request $request)
    {
        // Extract Google user data from the request
        $googleUser = (object) $request->input('google_user');

        $user = User::updateOrCreate(
            ['email' => $googleUser->email],
            [
                'password' => Hash::make(Str::password(12)),
                'social_id' => $googleUser->id,
                'is_verified' => true,
                'signup_type' => 'Google',
                'is_active' => true,
            ]
        );

        if ($user->profile) {
            $user->profile->update([
                'first_name' => $googleUser->user['given_name'],
                'last_name' => $googleUser->user['family_name'],
                'avatar_url' => $googleUser->attributes['avatar_original'],
            ]);
        } else {
            $user->profile()->create([
                'first_name' => $googleUser->user['given_name'],
                'last_name' => $googleUser->user['family_name'],
                'avatar_url' => $googleUser->attributes['avatar_original'],
            ]);
        }

        $token = JWTAuth::fromUser($user);

        $response = [
            'status_code' => 201,
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

    public function saveGoogleRequestPost(Request $request)
    {
        // Validate the incoming request working
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 422,
                'message' => $validator->errors()
            ], 422);
        }
        
        // Extract Google user data from the request
        $idToken = $request->id_token;

        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]); // Replace with your Google Client ID
        $payload = $client->verifyIdToken($idToken);

        if ($payload) {
             // Token is valid
            $email = $payload['email'];
            $firstName = $payload['given_name'];
            $lastName = $payload['family_name'];
            $avatarUrl = $payload['picture'] ?? null;

            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'password' => Hash::make(Str::random(12)), // Generate a random password for the user
                    'social_id' => $idToken,
                    'is_verified' => true,
                    'signup_type' => 'Google',
                    'is_active' => true,
                    'role' => 'user',
                ]
            );

            // Update or create user profile
            if ($user->profile) {
                $user->profile->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'avatar_url' => $avatarUrl,
                ]);
            } else {
                $user->profile()->create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'avatar_url' => $avatarUrl,
                ]);
            }

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'status_code' => 200,
                'message' => 'User Created Successfully',
                'access_token' => $token,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'fullname' => $firstName.' '.$lastName,
                        'role' => $user->role,
                    ]
                ]
            ]);
        } else {
            // Invalid token
            return response()->json([
                'status_code' => 401,
                'message' => 'Invalid Token'
            ], 401);
        }
    }

    public function loginUsingFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    public function callbackFromFacebook()
    {
        $facebookUser = Socialite::driver('facebook')->stateless()->user();

        $user = User::updateOrCreate(
            ['email' => $facebookUser->email],
            [
                'password' => Hash::make(Str::password(12)),
                'social_id' => $facebookUser->id,
                'is_verified' => true,
                'signup_type' => 'Facebook',
                'is_active' => true,
            ]
        );

        if ($user->profile) {
            $user->profile->update([
                'first_name' => $this->getFirstName($facebookUser->name),
                'last_name' => $this->getLastName($facebookUser->name),
                'avatar_url' => $facebookUser->avatar,
            ]);
        } else {
            $user->profile()->create([
                'first_name' => $this->getFirstName($facebookUser->name),
                'last_name' => $this->getLastName($facebookUser->name),
                'avatar_url' => $facebookUser->avatar,
            ]);
        }

        $token = JWTAuth::fromUser($user);

        $response = [
            'status_code' => 200,
            'message' => 'User successfully authenticated',
            'access_token' => $token,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $this->getFirstName($facebookUser->name),
                'last_name' => $this->getLastName($facebookUser->name)
            ]
        ];

        return response()->json($response);
    }

    public function saveFacebookRequest(Request $request)
    {
        // Extract Facebook user data from the request
        $facebookUser = (object) $request->input('facebook_user');

        $user = User::updateOrCreate(
            ['email' => $facebookUser->email],
            [
                'password' => Hash::make(Str::password(12)),
                'social_id' => $facebookUser->id,
                'is_verified' => true,
                'signup_type' => 'Facebook',
                'is_active' => true,
            ]
        );

        if ($user->profile) {
            $user->profile->update([
                'first_name' => $this->getFirstName($facebookUser->name),
                'last_name' => $this->getLastName($facebookUser->name),
                'avatar_url' => $facebookUser->avatar,
            ]);
        } else {
            $user->profile()->create([
                'first_name' => $this->getFirstName($facebookUser->name),
                'last_name' => $this->getLastName($facebookUser->name),
                'avatar_url' => $facebookUser->avatar,
            ]);
        }

        $token = JWTAuth::fromUser($user);

        $response = [
            'status' => 201,
            'message' => 'User successfully authenticated',
            'access_token' => $token,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $this->getFirstName($facebookUser->name),
                'last_name' => $this->getLastName($facebookUser->name)
            ]
        ];

        return response()->json($response);
    }

    private function getFirstName($fullName)
    {
        $names = explode(' ', $fullName);
        return count($names) > 2 ? $names[0] : $names[0];
    }

    private function getLastName($fullName)
    {
        $names = explode(' ', $fullName);
        return count($names) > 2 ? end($names) : (count($names) > 1 ? $names[1] : '');
    }
}
