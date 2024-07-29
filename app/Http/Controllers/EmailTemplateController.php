<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailTemplateController extends Controller
{
    /**
     * Display a paginated list of email templates.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Validate query parameters
        $validator = Validator::make($request->all(), [
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid pagination parameters'
            ], 400);
        }

        $limit = $request->input('limit', 10); // Default to 10 items per page
        $templates = EmailTemplate::paginate($limit);

        return response()->json([
            'templates' => $templates->items(),
            'total' => $templates->total(),
            'page' => $templates->currentPage(),
            'limit' => $templates->perPage(),
        ], 200);
    }
}
