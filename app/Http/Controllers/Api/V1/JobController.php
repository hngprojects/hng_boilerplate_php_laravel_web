<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function update(UpdateJobRequest $request, string $id)
    {
        $validated = $request->validated();

        $user = User::where('id', auth()->id())->first();

        $job = $user->jobs()->where('job_id', $id)->first();

        if (!$job) {
            return $this->jsonReponse([
                'status' => 'error',
                'message' => 'User not found',
                'data' => null
            ], Response::HTTP_NOT_FOUND);
        }

        if ($user) {

            $job = $job->update($validated);

            $job = $user->jobs()->where('job_id', $id)->first();

            return $this->jsonReponse([
                'message' => 'Job details updated successfully',
                'status_code' => 200,
                'data' => [
                    'title' => $job->title,
                    'description' => $job->description,
                    'location' => $job->location,
                    'salary' => $job->salary,
                    'job_type' => $job->job_type,
                    'company_name' => $job->company_name,
                    'created_at' => $job->created_at->toDateTimeString(),
                    'updated_at' => $job->updated_at->toDateTimeString(),
                ],
            ],  Response::HTTP_OK);
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
