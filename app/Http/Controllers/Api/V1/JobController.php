<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\GetJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class JobController extends Controller
{
    use ApiResponses;
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
    public function show(GetJobRequest $request, $id): JsonResponse
    {
        $user = $request->auth;
        $job = Job::findOrFail($id);

        return $this->successResponse(new JobResource($job));
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
