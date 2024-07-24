<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use Tymon\JWTAuth\Facades\JWTAuth;

class BlogDeleteController extends Controller
{
    /**
     * Delete a blog post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        try {
            // Ensure the user is authenticated
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'Invalid: either user not logged in or unauthenticated'], 401);
            }
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Invalid: either user not logged in or unauthenticated'], 401);
        }

        // Find the blog post by ID
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['error' => 'Blog post not found'], 404);
        }

        // Delete the blog post
        $blog->delete();

        return response()->json(['message' => 'Blog post deleted successfully'], 200);
    }
}
