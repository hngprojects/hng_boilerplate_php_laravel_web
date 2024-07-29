<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\CookiePreference;

class CookiePreferencesController extends Controller
{
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'preferences' => 'required|json',
        ]);

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
            $preferences = json_decode($data['preferences'], true);

            $cookiePreference = CookiePreference::updateOrCreate(
                ['user_id' => $userId],
                ['preferences' => $preferences, 'updated_at' => now()]
            );

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
            ], 500);
        }
    }
}
