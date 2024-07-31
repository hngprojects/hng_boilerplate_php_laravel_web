<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\HelpArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;

class HelpArticleController extends Controller
{


    public function store(Request $request)
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'status_code' => 401,
                'success' => false,
                'message' => 'Authentication failed'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|uuid|exists:users,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 422,
                'success' => false,
                'message' => 'Invalid input data.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $article = HelpArticle::create([
                'user_id' => $request->user_id,
                'title' => $request->title,
                'content' => $request->content,
            ]);

            return response()->json([
                'status_code' => 201,
                'success' => true,
                'message' => 'Help article created successfully.',
                'data' => $article
            ], 201);
        } catch (QueryException $e) {
            if ($e->getCode() === '23505') { // Unique violation error code
                return response()->json([
                    'status_code' => 409,
                    'success' => false,
                    'message' => 'An article with this title already exists.'
                ], 409);
            }

            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => 'Failed to create help article. Please try again later.',
                'error' => $e->getMessage() // Include error message
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => 'Failed to create help article. Please try again later.',
                'error' => $e->getMessage() // Include error message
            ], 500);
        }
    }

    public function update(Request $request, $articleId)
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'status_code' => 401,
                'success' => false,
                'message' => 'Authentication failed'
            ], 401);
        }

        // Validate the input
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'success' => false,
                'message' => 'Invalid input data.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $article = HelpArticle::find($articleId);

            if (!$article) {
                return response()->json([
                    'status_code' => 404,
                    'success' => false,
                    'message' => 'Help article not found.'
                ], 404);
            }

            // Check if the authenticated user is the author of the article
            if (Auth::id() !== $article->user_id) {
                return response()->json([
                    'status_code' => 403,
                    'success' => false,
                    'message' => 'You do not have permission to update this article.'
                ], 403);
            }

            $article->update($request->only(['title', 'content']));

            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => 'Help article updated successfully.',
                'data' => $article
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => 'Failed to update help article. Please try again later.',
                'error' => $e->getMessage() // Include error message
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => 'Failed to update help article. Please try again later.',
                'error' => $e->getMessage() // Include error message
            ], 500);
        }
    }
    public function destroy($articleId)
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'status_code' => 401,
                'success' => false,
                'message' => 'Authentication failed'
            ], 401);
        }

        try {
            // Find the article by ID
            $article = HelpArticle::find($articleId);

            if (!$article) {
                return response()->json([
                    'status_code' => 404,
                    'success' => false,
                    'message' => 'Help article not found.'
                ], 404);
            }

            // Ensure only the authenticated user can delete the article
            if ($article->user_id !== Auth::id()) {
                return response()->json([
                    'status_code' => 403,
                    'success' => false,
                    'message' => 'You do not have permission to access this resource.'
                ], 403);
            }

            // Delete the article
            $article->delete();

            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => 'Help article deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => 'Failed to delete help article. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getArticles(Request $request)
    {
        // Validate query parameters
        $validated = $request->validate([
            'page' => 'nullable|integer|min:1',
            'size' => 'nullable|integer|min:1|max:100',
            'category' => 'nullable|integer',
            'search' => 'nullable|string|min:3'
        ]);

        try {
            // Build the query
            $query = HelpArticle::query();

            // Apply search filter if provided
            if ($request->has('search')) {
                $query->where(function ($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->search . '%')
                        ->orWhere('content', 'like', '%' . $request->search . '%');
                });
            }

            // Apply category filter if provided
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            // Pagination
            $page = $request->get('page', 1);
            $size = $request->get('size', 10);
            $articles = $query->paginate($size, ['*'], 'page', $page);

            return response()->json([
                'status_code' => 200,
                'success' => true,
                'message' => 'Articles retrieved successfully.',
                'data' => [
                    'topics' => $articles->items(),
                    'pagination' => [
                        'page' => $articles->currentPage(),
                        'size' => $articles->perPage(),
                        'total_pages' => $articles->lastPage(),
                        'total_items' => $articles->total()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'success' => false,
                'message' => 'Failed to retrieve help articles. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function search(Request $request)
    {
        // Rate limiting
        $this->middleware('throttle:search,10'); // Allows 10 requests per minute, adjust as needed

        $title = $request->query('title');

        // Validate input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data.',
                'status_code' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Use article_id as defined in your schema
            $articles = HelpArticle::where('title', 'like', "%{$title}%")
                ->get(['article_id', 'title', 'content', 'user_id']);

            if ($articles->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No articles found.',
                    'status_code' => 404
                ], 404);
            }

            // Fetch author names
            $articles->transform(function ($article) {
                $user = User::find($article->user_id); // Ensure you have a User model
                $article->author = $user ? $user->first_name . ' ' . $user->last_name : 'Unknown';
                return $article;
            });

            return response()->json([
                'success' => true,
                'message' => 'Articles retrieved successfully.',
                'status_code' => 200,
                'topics' => $articles
            ], 200);
        } catch (\Exception $e) {
            // Log the error for debugging


            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve articles. Please try again later.',
                'status_code' => 500,
                'error' => $e->getMessage() // Include exception message in the response
            ], 500);
        }
    }
}
