<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
<<<<<<< HEAD
use App\Models\Job;

class JobController extends Controller
{
=======

use App\Http\Resources\JobResource;
use App\Models\Job;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class JobController extends Controller
{

>>>>>>> 0f1d53f (fixing-test)
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
<<<<<<< HEAD
    public function show(string $id)
    {
        //
=======
    public function show(string $id): JsonResponse
    {
        $job = Job::find($id);

        if (!$job) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Job not found',
                'error' => 'Not Found'
            ], 404);
        }

        return response()->json(new JobResource($job), 200);
>>>>>>> 0f1d53f (fixing-test)
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
