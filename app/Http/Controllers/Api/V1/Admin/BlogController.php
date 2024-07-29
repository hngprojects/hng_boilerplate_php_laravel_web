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
                ->select('id', 'title', 'content', 'author', 'created_at', 'blog_category_id')
                ->with('blog_category', 'images')
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
            $blogPosts = Blog::orderBy('created_at', 'desc')
                ->select('id', 'title', 'content', 'author', 'created_at', 'blog_category_id')
                ->with('images', 'blog_category')
                ->get();

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
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            // 'images' => ['required'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:500'],
            'author' => ['required', 'string', 'max:255'],
            'blog_category_id' => ['required', 'uuid', 'exists:blog_categories,id'],
        ]
        , [
            'blog_category_id.uuid' => 'The blog category does not exist.',
            'blog_category_id.exists' => 'The selected blog category does not exist.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }
        try {
            DB::beginTransaction();
            $blog = Blog::create([
                'title' => $request->get('title'),
                'content' => (string)$request->get('content'),
                'author' => $request->get('author'),
                'blog_category_id' => $request->get('blog_category_id'),
            ]);

            if($request->has('images')){
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
            }
            

            DB::commit();
            return response()->json([
                'message' => 'Blog post created successfully.',
                'status_code' => Response::HTTP_CREATED,
            ], Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            // Log::error('Error creating blog post: ' . $exception->getMessage());
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

            $blog = Blog::with('images')->find($id);

            if(!$blog){
                return response()->json([
                    'error' => 'Blog not found.',
                    'status_code' => Response::HTTP_NOT_FOUND,
                ], 404);
            }

            return response()->json([
                'data' => $blog,
                'message' => 'Blog post fetched sucessfully.',
                'status_code' => Response::HTTP_OK,
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            // Log::error('Error creating blog post: ' . $exception->getMessage());
            DB::rollBack();
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

            $blog->update([
                'title' => $request->get('title') ?? $blog->title,
                'content' => (string)$request->get('content') ?? $blog->content,
                'author' => $request->get('author') ?? $blog->author,
                'blog_category_id' => $request->get('blog_category_id') ?? $blog->blog_category_id,
            ]);

            if ($request->hasFile('images')) {
                // Delete old images
                foreach ($blog->images as $image) {
                    Storage::disk('public')->delete($image->image_url);
                    $image->delete();
                }

                // Upload new images
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
            }

            DB::commit();

            return response()->json([
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
            $blog = Blog::with('images')->find($id);

            if (!$blog) {
                return response()->json([
                    'message' => 'Blog with the given Id does not exist',
                    'status_code' => 404
                ], 404);
            }
            
            if(count($blog->images) > 0){
                foreach($blog->images as $image){
                    $image->delete();
                }
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
