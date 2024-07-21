<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    public function latest(Request $request)
    {
        try {
            // Get pagination parameters
            $page = $request->query('page', 1);
            $pageSize = $request->query('page_size', 10);

            // Validate the pagination parameters
            if (!is_numeric($page) || !is_numeric($pageSize) || $page <= 0 || $pageSize <= 0) {
                return response()->json(['error' => 'Invalid page or page_size parameter.'], 400);
            }

            // Fetch the latest blog posts with pagination
            $blogPosts = Blog::orderBy('created_at', 'desc')
                ->select('title', 'content', 'imageUrl', 'tags', 'author', 'created_at')
                ->paginate($pageSize, ['*'], 'page', $page);

            // Manually construct next and previous URLs with page_size parameter
            $nextPageUrl = $blogPosts->nextPageUrl();
            $previousPageUrl = $blogPosts->previousPageUrl();

            if ($nextPageUrl) {
                $nextPageUrl .= '&page_size=' . $pageSize;
            }

            if ($previousPageUrl) {
                $previousPageUrl .= '&page_size=' . $pageSize;
            }

            return response()->json([
                'count' => $blogPosts->total(),
                'next' => $nextPageUrl,
                'previous' => $previousPageUrl,
                'results' => $blogPosts->items(),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching latest blog posts: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error.'], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
        //
    }
}
