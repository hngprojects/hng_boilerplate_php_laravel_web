<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\WaitlistRequest;
use App\Models\WaitlistUser;
use Illuminate\Support\Facades\Mail;
use App\Mail\WaitlistConfirmation;
use Exception;

class WaitListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'message' => 'Welcome',
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            try {
                // Validate incoming request
                $requested_user = $request->validate([
                    'email' => 'required|email|unique:waitlist_users,email',
                    'full_name' => 'required|string|max:255',
                ]);
            } catch (Exception $e) {
                return response()->json([
                    "message"=> $e,
                    "status_code"=> 400,
                    "error"=> "Bad Request"
                ], 400);
            }
            
            // Store user information
            $waitlist_user = WaitlistUser::create($requested_user);

            // Send confirmation email
            Mail::to($waitlist_user->email)->send(new WaitlistConfirmation($waitlist_user));

            return response()->json([
                'message' => 'You are all signed up!',
            ], 201);
        } catch(Exception $e) {
            return response()->json([
                "message"=> "Bad Request",
                "status_code"=> 400,
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
