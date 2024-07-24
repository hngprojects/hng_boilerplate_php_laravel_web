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
                return response()->json([
                    'message' => 'You are not authorized to perform this action',
                    'status_code' => 403
                ], 403);
            }
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'message' => 'You are not authorized to perform this action',
                'status_code' => 403
            ], 403);
        }

        // Check if the blog post exists
        if (!Blog::find($id)) {
            return response()->json([
                'message' => 'Blog with the given Id does not exist',
                'status_code' => 404
            ], 404);
        }

        try {
            // Delete the blog post
            $blog = Blog::findOrFail($id);
            $blog->delete();

            return response()->json([
                'message' => 'Blog successfully deleted',
                'status_code' => 202
            ], 202);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error.',
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Handle method not allowed error.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function methodNotAllowed()
    {
        return response()->json([
            'message' => 'This method is not allowed.',
            'status_code' => 405
        ], 405);
    }
}
