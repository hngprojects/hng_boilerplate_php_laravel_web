<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Squeeze;
use Illuminate\Database\QueryException;

class SqueezeController extends Controller
{
    public function store(Request $request)
    {

        // Validate the incoming request data
        try {
            $validatedData = $request->validate([
                'email' => 'required|email|unique:squeezes,email',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'location' => 'required|string|max:255',
                'job_title' => 'required|string|max:255',
                'company' => 'required|string|max:255',
                'interests' => 'required|array',
                'referral_source' => 'required|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            // Store user data in the database
            $squeeze = Squeeze::create($validatedData);

            // Commented out email dispatch
            // SendSqueezeEmail::dispatch($squeeze);

            // Success response
            return response()->json([
                'message' => 'Your request has been received. You will get a template shortly.'
            ], 200);
        } catch (QueryException $e) {
            // Handle specific SQL error for unique constraint
            if ($e->getCode() == '23505') {
                return response()->json([
                    'message' => 'Email address already exists.',
                    'status_code' => 409
                ], 409);
            }
            // General error response
            return response()->json([
                'message' => 'Failed to submit your request',
                'status_code' => 400
            ], 400);
        }
    }
}
