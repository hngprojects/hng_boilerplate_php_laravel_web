<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WaitlistUser;
use Illuminate\Support\Facades\Mail;
use App\Mail\WaitlistConfirmation;
use Illuminate\Support\Facades\Log;

class WaitlistController extends Controller
{
    /**
     * Display a paginated list of waitlist users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', WaitlistUser::class);

            $page = $request->query('page', 1);
            $limit = $request->query('limit', 10);
    
            $waitlistUsers = WaitlistUser::paginate($limit, ['*'], 'page', $page);
    
            return response()->json([
                'users' => $waitlistUsers->items(),
                'page' => $waitlistUsers->currentPage(),
                'limit' => $waitlistUsers->perPage(),
                'total_users' => $waitlistUsers->total(),
                'status_code' => 200,
                'message' => 'Retrieved waitlist users successfully'
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'message' => 'Not found',
                'status_code' => 404,
            ], 404);
        }

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
