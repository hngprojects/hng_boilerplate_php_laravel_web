<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'location' => 'string|max:255',
            'job_type' => 'string|max:255',
            'company_name' => 'string|max:255',
            'organisation_id' => 'string|exists:organisations,org_id', // Ensure org_id is referenced
        ]);

        if ($validator->fails()) {
            Log::error('Validation errors:', ['errors' => $validator->errors()]);
            return response()->json([
                'message' => 'Invalid request data',
                'errors' => $validator->errors(),
                'status_code' => 400,
            ], 400);
        }

        try {
            // Get the authenticated user
            $user = $request->user();

            // Find the job by ID
            $job = Job::find($id);

            if (!$job) {
                return response()->json([
                    'message' => 'Job not found',
                    'status_code' => 404,
                ], 404);
            }

            // Check if the user has permission to update the job
            // Assuming user has a `canUpdateJob` method to check permissions
            if (!$user->canUpdateJob($job)) {
                Log::error('User not authorized to update job:', ['user_id' => $user->id, 'job_id' => $id]);
                return response()->json([
                    'message' => 'User not authorized to update job',
                    'status_code' => 403,
                ], 403);
            }

            // Update the job listing
            $job->update($request->only([
                'title',
                'description',
                'location',
                'job_type',
                'company_name',
                'organisation_id',
            ]));

            return response()->json([
                'message' => 'Job listing updated successfully',
                'status_code' => 200,
                'data' => $job,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'status_code' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
