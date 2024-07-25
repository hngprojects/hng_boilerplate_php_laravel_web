<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogCreateRequest;
use App\Models\Blog;
use Exception;
use App\Models\BlogImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
                ->select('id', 'title', 'content', 'author', 'created_at')
                ->with('tags', 'images')
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
    public function store(BlogCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $blog = Blog::create([
                'title' => $request->get('title'),
                'content' => (string)$request->get('content'),
                'author' => $request->get('author'),
            ]);

            foreach ($request->file('images') as $image) {
                $saved = Storage::disk('public')->put('blog_header', $image);
                if ($saved) {
                    BlogImage::create([
                        'image_url' => $saved,
                        'blog_id' => $blog->id,
                    ]);
                } else {
                    throw new \Exception('Error saving image');
                }
            }

            $blog->tags()->createMany($request->get('tags'));

            DB::commit();
            return response()->json([
                'message' => 'Blog post created successfully.',
                'status_code' => Response::HTTP_CREATED,
            ], Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            Log::error('Error creating blog post: ' . $exception->getMessage());
            DB::rollBack();
            return response()->json(['error' => 'Internal server error.'], 500);
        }
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
        try {
            $blog = Blog::find($id);

            if (!$blog) {
                return response()->json([
                    'message' => 'Blog with the given Id does not exist',
                    'status_code' => 404
                ], 404);
            }

            $blog->delete();

            return response()->json([
                'message' => 'Blog successfully deleted',
                'status_code' => 202
            ], 202);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal server error.',
                'status_code' => 500
            ], 500);
        }
    }
}
