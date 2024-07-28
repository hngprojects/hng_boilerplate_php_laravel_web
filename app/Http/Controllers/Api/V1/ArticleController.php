<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function search(Request $request)
{
    $title = $request->query('title');

    if (!$title) {
        return response()->json([
            'success' => false,
            'message' => 'Title query parameter is required',
            'status_code' => 400
        ], 400);
    }

    $articles = Article::where('title', 'LIKE', '%' . $title . '%')->get();

    if ($articles->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No article matches the title search param.',
            'status_code' => 404
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'Articles found',
        'status_code' => 200,
        'topics' => $articles->map(function ($article) {
            return [
                'article_id' => $article->article_id,
                'author' => $article->author->name,
                'title' => $article->title,
                'content' => $article->content
            ];
        })
    ], 200);
}

}
