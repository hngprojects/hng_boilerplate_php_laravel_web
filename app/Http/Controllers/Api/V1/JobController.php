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
        //Find the post by Id
        $job = Job::find($id);
        // $job = Job::all();
        // dd($job);

        // Check if the job exists
        if (!$job) {
            return response()->json(['message' => 'Job not found', 'error' => 'Not Found'], 404);
        }

        // Check if the authenticated user is the owner of the job or related to the job
        if (!$job->users->contains(Auth::id())) {
            return response()->json(['message' => 'Unauthorized', 'error' => 'Bad Request'], 400);
        }

        // Delete the post
        $job->delete();

        // Return a response
        return response()->json(['message' => 'Job deleted successfully'], 200);
    }
}
