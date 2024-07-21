<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Optional query parameters
            $limit = $request->query('limit', 100);
            $offset = $request->query('offset', 0);
            $parent_id = $request->query('parent_id', null);

            // Validate query parameters
            $request->validate([
                'limit' => 'integer|min:1',
                'offset' => 'integer|min:0',
                'parent_id' => 'integer|nullable',
            ]);

        $categories = Category::when($parent_id, function ($query) use ($parent_id) {
            return $query->where('parent_id', $parent_id);
        })
        ->limit($limit)
        ->offset($offset)
        ->get(['id', 'name', 'description', 'slug', 'parent_id']);

        \Log::info($categories);

        return response()->json([
            'status_code' => 200,
            'categories' => $categories,
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status_code' => 400,
            'error' => [
                'code' => 'INVALID_QUERY_PARAMETER',
                'message' => 'The provided query parameter is invalid.',
                'details' => [
                    'invalid_parameter' => $e->validator->errors()->keys()[0],
                    'reason' => $e->validator->errors()->first(),
                ],
            ],
        ], 400);
    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'error' => [
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'An unexpected error occurred while processing your request.',
                'details' => [
                    'support_email' => 'support@example.com',
                ],
            ],
        ], 500);
    }
    }
}
