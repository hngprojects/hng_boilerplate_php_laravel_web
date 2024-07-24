<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WaitlistUser;
use Illuminate\Support\Facades\Mail;
use App\Mail\WaitlistConfirmation;
use Illuminate\Support\Facades\Log;

class WaitListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            
            // Validating post request
            $validatedData = $request->validate([
                'email' => 'required|email|unique:waitlist_users,email',
                'full_name' => 'required|string|min:1'
            ]);

            // Store user information
            $waitlist_user = WaitlistUser::create($validatedData);
    
            // Send confirmation email
            Mail::to($waitlist_user->email)->send(new WaitlistConfirmation($waitlist_user));
    
            // Success response
            return response()->json([
                'message' => 'You are all signed up!',
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {

            // Error response for validation errors
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation failed',
                'status_code' => 422
            ], 422);

        } catch (\Exception $e) {

            // Generic error response for other exceptions
            return response()->json([
                'error' => 'Bad Request',
                'message' => 'An error occurred',
                'status_code' => 400
            ], 400);
            
        }
    }    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }  
}
