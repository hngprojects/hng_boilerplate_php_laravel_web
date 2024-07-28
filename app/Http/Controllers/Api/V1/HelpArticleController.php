<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\HelpArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HelpArticleController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'page' => 'nullable|integer|min:1',
            'size' => 'nullable|integer|min:1|max:100',
            'category' => 'nullable|integer',
            'search' => 'nullable|string|min:3'
        ]);

        try {
            $query = HelpArticle::query();

            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            if ($request->has('search')) {
                $query->where(function ($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->search . '%')
                        ->orWhere('content', 'like', '%' . $request->search . '%');
                });
            }

            $page = $request->get('page', 1);
            $size = $request->get('size', 10);
            $articles = $query->paginate($size, ['*'], 'page', $page);

            return response()->json([
                'status_code' => 200,
                'success' => true,
                'data' => [
                    'articles' => $articles->items(),
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
                'message' => 'Failed to retrieve help articles. Please try again later.'
            ], 500);
        }
    }
}
