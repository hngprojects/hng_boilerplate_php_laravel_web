<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CookiePreference;
use Illuminate\Support\Str; // Import Str for UUID generation

class CookiePreferencesController extends Controller
{
    public function update(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|uuid|exists:users,id',  // Ensure user_id is a valid UUID and exists in the users table
            'preferences' => 'required|array',  // Ensure preferences is an array
            'preferences.analytics_cookies' => 'boolean',  // Optional: validate each preference key if needed
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
            $data = $request->only('user_id', 'preferences');
            $userId = $data['user_id'];
            $preferences = $data['preferences'];  // Preferences should be an array

            // Check if a cookie preference record exists for the given user_id
            $cookiePreference = CookiePreference::where('user_id', $userId)->first();

            if ($cookiePreference) {
                // If a record exists, update it
                $cookiePreference->update(['preferences' => $preferences]);
            } else {
                // If no record exists, create a new one with a generated UUID
                $cookiePreference = CookiePreference::create([
                    'id' => (string) Str::uuid(),  // Generate a UUID
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
            // Output the error details directly
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => 'Failed to update cookie preferences. Please try again later.',
                'error' => $e->getMessage(),  // Include the exception message
            ], 500);
        }
    }
}
