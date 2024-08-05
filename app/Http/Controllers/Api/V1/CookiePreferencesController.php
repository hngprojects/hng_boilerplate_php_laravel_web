<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CookiePreference;
use Illuminate\Support\Str;

class CookiePreferencesController extends Controller
{
    public function update(Request $request)
    {
        // Get the authenticated user's ID
        $userId = auth()->id();

        // Validation rules
        $validator = Validator::make($request->all(), [
            'preferences' => 'required|array',
            'preferences.analytics_cookies' => 'boolean',
            'preferences.marketing_cookies' => 'boolean',
            'preferences.functional_cookies' => 'boolean',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'success' => false,
                'message' => 'Invalid input data.',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $preferences = $request->input('preferences');

            // Check if a cookie preference record exists for the given user_id
            $cookiePreference = CookiePreference::where('user_id', $userId)->first();

            if ($cookiePreference) {
                // If a record exists, update it
                $cookiePreference->update(['preferences' => $preferences]);
            } else {
                // If no record exists, create a new one with a generated UUID
                $cookiePreference = CookiePreference::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $userId,
                    'preferences' => $preferences
                ]);
            }

            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => 'Cookie preferences updated successfully.',
                'data' => [
                    'user_id' => $userId,
                    'preferences' => $preferences,
                    'updated_at' => $cookiePreference->updated_at,
                ],
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => 'Failed to update cookie preferences. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPreferences(Request $request)
    {
        // Get the authenticated user's ID
        $userId = auth()->id();

        try {
            // Retrieve cookie preferences from the database
            $cookiePreference = CookiePreference::where('user_id', $userId)->first();

            // Check if preferences were found
            if (!$cookiePreference) {
                return response()->json([
                    'status_code' => 404,
                    'success' => false,
                    'message' => 'No cookie preferences found for the given user ID.'
                ], 404);
            }

            return response()->json([
                'status_code' => 200,
                'success' => true,
                'data' => [
                    'user_id' => $cookiePreference->user_id,
                    'preferences' => $cookiePreference->preferences,
                    'updated_at' => $cookiePreference->updated_at
                ],
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => 'Failed to retrieve cookie preferences. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
