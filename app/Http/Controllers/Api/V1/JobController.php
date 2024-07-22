<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

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
                'message' => 'Invalid job post id',
            ], Response::HTTP_NOT_FOUND);
        }

        $updateJob = $job->update($validated);

        if ($updateJob) {
            $job->users()->attach(auth()->id());

            return response()->json([
                'message' => 'Job details updated successfully',
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
        //
    }
}
