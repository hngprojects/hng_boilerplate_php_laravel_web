<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BlogCategoriesController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        try {
            DB::beginTransaction();
            BlogCategory::create([
                'name' => $request->get('name'),
                'description' => (string)$request->get('description')
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Blog category created successfully.',
                'status_code' => Response::HTTP_CREATED,
            ], Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            Log::error('Error creating blog post: ' . $exception->getMessage());
            DB::rollBack();
            return response()->json(['error' => 'Internal server error.'], 500);
        }
    }
}
