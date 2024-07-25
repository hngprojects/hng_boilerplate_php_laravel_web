<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateJobRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Models\Job;

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
                'message' => 'Job listings retrieved successfully!',
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

    public function update(UpdateJobRequest $request, string $id): JsonResponse
    {
        if (empty($id)) {
            return response()->json([
                'message' => 'Job post id is missing',
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $validated = $request->validated();

        $job = Job::find($id);
        if (!$job->users->contains(auth()->id())) {
            return response()->json([
                'message' => 'Invalid job post id!',
            ], Response::HTTP_NOT_FOUND);
        }

        $updateJob = $job->update($validated);

        if ($updateJob) {
            $job->users()->attach(auth()->id());

            return response()->json([
                'message' => 'Job details updated successfully!!!',
                'status_code' => Response::HTTP_OK,
                'data' =>
                [
                    'title' => $job->title,
                    'description' => $job->description,
                    'location' => $job->location,
                    'salary' => $job->salary,
                    'job_type' => $job->job_type,
                    'company_name' => $job->company_name,
                    'created_at' => $job->created_at->toDateTimeString(),
                    'updated_at' => $job->updated_at->toDateTimeString(),
                ],
            ]);
        }
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
