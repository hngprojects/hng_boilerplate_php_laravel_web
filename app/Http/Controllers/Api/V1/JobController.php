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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input parameters.',
                'errors' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $perPage = $request->input('size', 15);
        $jobs = Job::paginate($perPage);

        return response()->json([
            'message' => 'Job listings retrieved successfully.',
            'data' => collect($jobs->items())->map(function ($job) {
                return [
                    'title' => $job->title,
                    'description' => $job->description,
                    'location' => $job->location,
                    'salary' => $job->salary,
                ];
            }),
            'pagination' => [
                'current_page' => $jobs->currentPage(),
                'total_pages' => $jobs->lastPage(),
                'page_size' => $jobs->perPage(),
                'total_items' => $jobs->total(),
            ],
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admin users can create job listings.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'salary' => 'nullable|string',
            'job_type' => 'required|string',
            'experience_level' => 'nullable|string',
            'work_mode' => 'nullable|string',
            'benefits' => 'nullable|string',
            'deadline' => 'nullable|date',
            'key_responsibilities' => 'nullable|string',
            'qualifications' => 'nullable|string',
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
            ['user_id' => auth()->id()]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Job listing created successfully.',
            'data' => $job
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $job = Job::findOrFail($id);

        return response()->json([
            'id' => $job->id,
            'title' => $job->title,
            'description' => $job->description,
            'location' => $job->location,
            'salary' => $job->salary,
            'job_type' => $job->job_type,
            'experience_level' => $job->experience_level,
            'work_mode' => $job->work_mode,
            'benefits' => $job->benefits,
            'deadline' => $job->deadline,
            'key_responsibilities' => $job->key_responsibilities,
            'qualifications' => $job->qualifications,
            'created_at' => $job->created_at,
            'updated_at' => $job->updated_at,
        ], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admin users can update job listings.',
            ], Response::HTTP_FORBIDDEN);
        }

        $job = Job::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'string',
            'description' => 'string',
            'location' => 'string',
            'salary' => 'nullable|string',
            'job_type' => 'string',
            'experience_level' => 'nullable|string',
            'work_mode' => 'nullable|string',
            'benefits' => 'nullable|string',
            'deadline' => 'nullable|date',
            'key_responsibilities' => 'nullable|string',
            'qualifications' => 'nullable|string',
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

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admin users can delete job listings.',
            ], Response::HTTP_FORBIDDEN);
        }

        $job = Job::findOrFail($id);
        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job listing deleted successfully.'
        ], Response::HTTP_OK);
    }

}
