<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\User;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
                ->select('id', 'title', 'content', 'author', 'created_at', 'category', 'image_url')
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
                'status_code' => 200,
            ], 200);
        } catch (\Exception $e) {
            // Log::error('Error fetching latest blog posts: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error.'], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $blogPosts = Blog::with('comments')
            ->orderBy('created_at', 'desc')
            ->select('id', 'title', 'content', 'author', 'created_at', 'category', 'image_url')
            ->get();

            $blogPosts = $blogPosts->map(function ($blog) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'content' => $blog->content,
                    'published_date' => $blog->created_at?->toIso8601String(),
                    'updated_date' => $blog->updated_at?->toIso8601String(),
                    'author_id' => $blog->author_id,
                    'category' => $blog->category,
                    'image_url' => $blog->image_url,
                    'comments' => $blog->comments->map(function ($comment) {
                        return [
                            'id' => $comment->id,
                            'content' => $comment->content,
                            'created_at' => $comment->created_at?->toIso8601String(),
                        ];
                    }),
                ];
            });

            return response()->json([
                'data' => $blogPosts,
                'message' => 'All blog posts retrieved successfully',
                'status_code' => 200,
            ], 200);
        }catch(Exception $exception){
            return response()->json(['error' => 'Internal server error.'], 500);
        }

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
        $author = User::find(Auth::id());

        if (!$author) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:255',
            'image_url' => 'required|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        try {
            DB::beginTransaction();

            $savedPath = Storage::disk('public')->put('images', $request->file('image_url'));
            $imageUrl = 'storage/' . $savedPath;

            $blog = Blog::create([
                'title' => $request->get('title'),
                'content' => $request->get('content'),
                'author' => $author->name ?? '',
                'image_url' => $imageUrl,
                'category' => $request->get('category'),
                'author_id' => $author->id,
            ]);

            DB::commit();

            return response()->json([
                'id' => $blog->id,
                'title' => $blog->title,
                'image_url' => $blog->image_url,
                'content' => $blog->content,
                'published_date' => $blog->created_at->toIso8601String(),
                'updated_date' => $blog->updated_at->toIso8601String(),
                'author_id' => $blog->author_id,
                'category' => $blog->category,
                'comments' => $blog->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'created_at' => $comment->created_at->toIso8601String(),
                    ];
                }),
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
    public function show(Request $request, string $id)
    {
        try {
            $blog = Blog::with('comments')->find($id);

            if(!$blog){
                return response()->json([
                    'error' => 'Blog not found.',
                    'message' => 'Blog not found.',
                    'status_code' => Response::HTTP_NOT_FOUND,
                ], 404);
            }

            return response()->json([
                'data' => [
                    'title' => $blog->title,
                    'category' => $blog->category,
                    'content' => $blog->content,
                    'image_url' => $blog->image_url,
                    'published_date' => $blog->created_at?->toIso8601String(),
                    'updated_date' => $blog->updated_at?->toIso8601String(),
                    'author_id' => $blog->author_id,
                    'comments' => $blog->comments->map(function ($comment) {
                        return [
                            'id' => $comment->id,
                            'content' => $comment->content,
                            'created_at' => $comment->created_at?->toIso8601String(),
                        ];
                }),
                ],
                'message' => 'Blog post details fetched sucessfully.',
                'status_code' => Response::HTTP_OK,
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['error' => 'Internal server error.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $blog = Blog::find($id);

            if(!$blog){
                return response()->json([
                    'error' => 'Blog not found.',
                    'status_code' => Response::HTTP_NOT_FOUND,
                ], 404);
            }

            //user not the author
            if($blog->author_id != Auth::id()){
                return response()->json([
                    'status' => 'Forbidden',
                    'message' => 'Not authorized to edit this blog post',
                    'status_code' => 403
                ], 403);
            }
            if ($request->hasFile('image_url')) {
                // Delete old image
                if(Storage::disk('public')->exists($blog->image_url)) Storage::disk('public')->delete($blog->image_url);

                // Upload new image
                $saved_image = Storage::disk('public')->put('images', $request->file('image_url'));
                $saved = 'storage/'.$saved_image;
            }else{
                $saved = $blog->image_url;
            }

            $blog->update([
                'title' => $request->get('title') ?? $blog->title,
                'content' => $request->get('content') ?? $blog->content,
                'category' => $request->get('category') ?? $blog->category,
                'image_url' => $saved,
            ]);

            DB::commit();

            return response()->json([
                'data' => $blog,
                'message' => 'Blog post updated successfully.',
                'status_code' => Response::HTTP_OK,
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['error' => 'Internal server error.'], 500);
        }
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
            if($blog->author_id != Auth::id()){
                return response()->json([
                    'status' => 'Forbidden',
                    'message' => 'Not authorized to delete this blog post',
                    'status_code' => 403
                ], 403);
            }

            $blog->delete();

            return response()->noContent();
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal server error.',
                'status_code' => 500
            ], 500);
        }
    }
}
