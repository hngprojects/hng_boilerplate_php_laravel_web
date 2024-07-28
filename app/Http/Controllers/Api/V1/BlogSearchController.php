<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BlogSearchController extends Controller
{
    public function search(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'author' => 'nullable|string',
                'title' => 'nullable|string',
                'content' => 'nullable|string',
                'tags' => 'nullable|string',
                'created_date' => 'nullable|date_format:Y-m-d',
                'page' => 'nullable|integer|min:1',
                'page_size' => 'nullable|integer|min:1|max:100',
            ]);

            $page = $validatedData['page'] ?? 1;
            $pageSize = $validatedData['page_size'] ?? 20;

            $query = Blog::query();

            if (!empty($validatedData['author'])) {
                $query->where('author', 'like', '%' . $validatedData['author'] . '%');
            }
            if (!empty($validatedData['title'])) {
                $query->where('title', 'like', '%' . $validatedData['title'] . '%');
            }
            if (!empty($validatedData['content'])) {
                $query->where('content', 'like', '%' . $validatedData['content'] . '%');
            }
            if (!empty($validatedData['tags'])) {
                $query->where('tags', 'like', '%' . $validatedData['tags'] . '%');
            }
            if (!empty($validatedData['created_date'])) {
                $query->whereDate('created_at', '>=', $validatedData['created_date']);
            }

            $blogs = $query->paginate($pageSize, ['*'], 'page', $page);

            $response = [
                'current_page' => $blogs->currentPage(),
                'total_pages' => $blogs->lastPage(),
                'total_results' => $blogs->total(),
                'blogs' => $this->formatBlogs($blogs->items()),
                'meta' => [
                    'has_next' => $blogs->hasMorePages(),
                    'total' => $blogs->total(),
                    'next_page' => $blogs->hasMorePages() ? $blogs->currentPage() + 1 : null,
                    'prev_page' => $blogs->currentPage() > 1 ? $blogs->currentPage() - 1 : null,
                ],
            ];

            // if ($blogs->total() == 0) {
            //     $response['message'] = 'No blogs found matching the search criteria.';
            // }

            Log::info('Blog search request', ['params' => $validatedData, 'results' => $blogs->total()]);

            return response()->json($response);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'error' => $e->errors(),
                'status_code' => 422
            ], 422);
        } catch (\Exception $e) {
            Log::error('Blog search error', ['message' => $e->getMessage(), 'params' => $request->all()]);

            return response()->json([
                'message' => 'An error occurred while searching blogs',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }

    private function formatBlogs($blogs)
    {
        return array_map(function ($blog) {
            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'content' => $blog->content,
                'author' => $blog->author,
                'created_date' => $blog->created_at->toDateTimeString(),
                'tags' => explode(',', $blog->tags),
            ];
        }, $blogs);
    }
}
