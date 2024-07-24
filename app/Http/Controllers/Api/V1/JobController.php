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
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $size = $request->input('size', 10);
    
            $jobs = Job::paginate($size, ['id', 'title', 'description', 'location', 'salary', 'job_type'], 'page', $page);
    
            return response()->json([
                'message' => 'Job listings retrieved successfully.',
                'data' => $jobs->items(),
                'pagination' => [
                    'current_page' => $jobs->currentPage(),
                    'total_pages' => $jobs->lastPage(),
                    'page_size' => $jobs->perPage(),
                    'total_items' => $jobs->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve job listings.',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
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
    public function destroy(string $id, Request $request)
    {
        // Get the authenticated user
        if(!$user = $request->user()) {
            return response()->json(['message' => 'Unauthorized', 'error' => 'Bad Request'], 400);
        }

        // Get organization_id from the request
        if(!$orgId = $request->organization_id) {
            return response()->json(['message' => 'Unauthorized', 'error' => 'Bad Request'], 400);
        }

        //Find the post by Id
        $job = Job::find($id);

        // Check if the job exists
        if (!$job) {
            return response()->json(['message' => 'Job not found', 'error' => 'Not Found'], 404);
        }

        // Check if the authenticated user is the owner of the job or related to the job
        // if (!$job->users->contains(Auth::id())) {
        //     return response()->json(['message' => 'Unauthorized', 'error' => 'Bad Request'], 400);
        // }

        // Delete the post
        $job->delete();

        // Return a response
        return response()->json(['message' => 'Job deleted successfully'], 200);
    }
}
