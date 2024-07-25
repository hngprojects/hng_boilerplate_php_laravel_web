<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
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
        //
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
        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
                'status-code' => 401
            ], 401);
        }
        // $user = Auth::user();

        // if (!$user) {
        //     return response()->json([
        //         'status' => 'Unauthorized',
        //         'message' => 'Unauthorized.',
        //         'status_code' => 401,
        //     ], 401);
        // }

        // Get organization_id from the request
        // if(!$orgId = $request->organization_id) {
        //     return response()->json(['message' => 'Unauthorized', 'error' => 'Bad Request'], 400);
        // }

        //Find the post by Id
        $job = Job::find($id);

        // Check if the job exists
        if (!$job) {
            return response()->json(['message' => 'Job not found', 'error' => 'Not Found'], 404);
        }

        // Delete the job
        $job->delete();

        // Return a response
        return response()->json(['message' => 'Job deleted successfully'], 200);
    }

    // public function destroy(string $id, Request $request)
    // {
    //     try {
    //         \Log::info('Deleting job with ID:', ['id' => $id]);

    //         // Find the job by Id
    //         $job = Job::find($id);

    //         // Check if the job exists
    //         if (!$job) {
    //             \Log::warning('Job not found:', ['id' => $id]);
    //             return response()->json(['message' => 'Job not found', 'error' => 'Not Found'], 404);
    //         }

    //         // Delete the job
    //         $job->delete();
    //         \Log::info('Job deleted successfully:', ['id' => $id]);

    //         // Return a response
    //         return response()->json(['message' => 'Job deleted successfully'], 200);
    //     } catch (\Exception $e) {
    //         \Log::error('Error deleting job:', ['error' => $e->getMessage()]);
    //         return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
    //     }
    // }

}
