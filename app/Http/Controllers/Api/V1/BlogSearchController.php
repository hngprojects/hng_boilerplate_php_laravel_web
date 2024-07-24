<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BlogSearchController extends Controller
{
    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'author' => 'nullable|string',
            'title' => 'nullable|string',
            'content' => 'nullable|string',
            'tags' => 'nullable|string',
            'createdDate' => 'nullable|date_format:Y-m-d',
            'page' => 'nullable|integer|min:1',
            'pageSize' => 'nullable|integer|min:1|max:100',
        ]);

        $page = $validatedData['page'] ?? 1;
        $pageSize = $validatedData['pageSize'] ?? 20;

        try {
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
            if (!empty($validatedData['createdDate'])) {
                $query->whereDate('created_at', '>=', $validatedData['createdDate']);
            }

            $blogs = $query->paginate($pageSize, ['*'], 'page', $page);

            $response = [
                'currentPage' => $blogs->currentPage(),
                'totalPages' => $blogs->lastPage(),
                'totalResults' => $blogs->total(),
                'blogs' => $this->formatBlogs($blogs->items()),
                'meta' => [
                    'hasNext' => $blogs->hasMorePages(),
                    'total' => $blogs->total(),
                    'nextPage' => $blogs->hasMorePages() ? $blogs->currentPage() + 1 : null,
                    'prevPage' => $blogs->currentPage() > 1 ? $blogs->currentPage() - 1 : null,
                ],
            ];

            Log::info('Blog search request', ['params' => $validatedData, 'results' => $blogs->total()]);

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Blog search error', ['message' => $e->getMessage(), 'params' => $validatedData]);

            return response()->json([
                'message' => 'An error occurred while searching blogs',
                'error' => $e->getMessage(),
                'statusCode' => 500
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
                'createdDate' => $blog->created_at->toDateTimeString(),
                'tags' => explode(',', $blog->tags),
            ];
        }, $blogs);
    }
}
