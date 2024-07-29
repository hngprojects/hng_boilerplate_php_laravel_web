<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer|min:1',
            'size' => 'nullable|integer|min:1',
            'location' => 'nullable|string',
            'job_type' => 'nullable|string',
            'work_mode' => 'nullable|string',
            'company_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input parameters.',
                'errors' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $query = Job::query();      

        $perPage = $request->input('size', 15);
        $jobs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $jobs->items(),
            'pagination' => [
                'current_page' => $jobs->currentPage(),
                'per_page' => $jobs->perPage(),
                'total_pages' => $jobs->lastPage(),
                'total_items' => $jobs->total(),
            ],
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $organisation = $user->organisations()->first();

        if (!$organisation) {
            return response()->json([
                'success' => false,
                'message' => 'User is not associated with any organisation.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'salary' => 'required|string',
            'deadline' => 'nullable|date',
            'work_mode' => 'nullable|string',
            'job_type' => 'required|string',
            'experience_level' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $job = Job::create(array_merge(
            $validator->validated(),
            [
                'user_id' => $user->id,
                'organisation_id' => $organisation->org_id,
                'company_name' => $organisation->name
            ]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Job listing created successfully.',
            'data' => $job
        ], Response::HTTP_CREATED);
    }

         
    

    public function update(Request $request, $id)
    {
    $job = Job::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'title' => 'string',
        'description' => 'string',
        'location' => 'string',
        'salary' => 'string',
        'deadline' => 'date',
        'company_name' => 'string',
        'work_mode' => 'string',
        'job_type' => 'string',
        'experience_level' => 'string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], Response::HTTP_BAD_REQUEST);
    }

    $job->update($validator->validated());

    return response()->json([
        'success' => true,
        'message' => 'Job listing updated successfully.',
        'data' => $job
    ], Response::HTTP_OK);
    }

    public function show($id)
    {
    $validator = Validator::make(['job_id' => $id], [
        'job_id' => 'required|uuid'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid job_id.',
            'errors' => $validator->errors(),
        ], Response::HTTP_BAD_REQUEST);
    }

    $job = Job::find($id);

    if (!$job) {
        return response()->json([
            'success' => false,
            'message' => 'Job listing not found.',
        ], Response::HTTP_NOT_FOUND);
    }

    return response()->json([
        'success' => true,
        'data' => $job
    ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
    $job = Job::findOrFail($id);
    $job->delete();

    return response()->json([
        'success' => true,
        'message' => 'Job listing deleted successfully.'
    ], Response::HTTP_OK);
    }


}
