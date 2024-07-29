<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    //createcommet
    public function createComment(Request $request, $blogId)
    {
        try {
            $user = auth('api')->user();
            $request->validate([
                'content' => 'required|string',
            ]);

            $comment = Comment::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'blog_id' => $blogId,
                'name' => $user->name,
                'content' => $request->content,
                'likes' => 0,
                'dislikes' => 0
            ]);

            return response()->json([
                'status' => 201,
                'message' => 'Comment created successfully',
                'data' => $comment
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to create comment',
            ], 500);
        }
    }


    //replycommet
    public function replyComment(Request $request, $commentId)
    {
        try {
            Log::info('Reached the replyComment method');
            Log::info('Request data:', $request->all());
            
            $user = auth('api')->user();
            $request->validate([
                'content' => 'required|string'
            ]);
            Log::info('Validation passed');
            $originalComment = Comment::findOrFail($commentId);

            $reply = Comment::create([
                'user_id' => $user->id,
                'blog_id' => $originalComment->blog_id,
                'name' => $user->name,
                'content' => $request->content,
                'likes' => 0,
                'dislikes' => 0,
            ]);
            Log::info('Reply comment created:', $reply->toArray());
            return response()->json([
                'status' => 201,
                'message' => 'Reply created successfully',
                'data' => $reply
            ], 201);
        } catch (
            \Exception  $e
        ) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to create reply',
            ], 500);
        }
    }

    //likecommet
    public function likeComment($commentId)
    {
        try {
            $comment = Comment::findOrFail($commentId);
            $comment->likes += 1;
            $comment->save();

            return response()->json([
                'status' => 200,
                'message' => 'Comment liked successfully',
                'data' => $comment
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to dislike comment',
            ], 500);
        }
    }

    //dislikecommet
    public function dislikeComment($commentId)
    {
        try {
            $comment = Comment::findOrFail($commentId);
            $comment->dislikes += 1;
            $comment->save();

            return response()->json([
                'status' => 200,
                'message' => 'Comment disliked successfully',
                'data' => $comment
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to dislike comment',
            ], 500);
        }
    }

    //editcommet
    public function editComment(Request $request, $commentId)
    {
       try {
            $user = auth('api')->user();
            $request->validate([
                'content' => 'required|string'
            ]);

            $comment = Comment::findOrFail($commentId);

            if ($user->id !== $comment->user_id) {
                return response()->json([
                    'status' => 403,
                    'message' => "Forbidden: You cannot edit this comment"
                ], 403);
            }

            $comment->content = $request->content;
            $comment->save();

            return response()->json([
                'status' => 200,
                'message' => 'Comment edited successfully',
                'data' => $comment
            ], 200);
       } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to edit comment',
            ], 500);
       }
    }

    //Delete commet
    public function deleteComment($commentId)
    {
       try {
            $user = auth('api')->user();
            $comment = Comment::findOrFail($commentId);

            if ($comment->user_id !== $user->id) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Forbidden: You cannot delete this comment',
                ], 403);
            }

            $comment->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Comment deleted successfully',
            ], 200);
       } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete comment',
            ], 500);
       }
    }

    //get comment for a blog
    public function getBlogComments($blogId)
    {
       try {
            $comments = Comment::where('blog_id', $blogId)->get();

            return response()->json([
                'status' => 200,
                'message' => 'Comments retrieved successfully',
                'data' => $comments
            ], 200);
       } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to retrieve comments',
            ], 500);
       }
    }
}
