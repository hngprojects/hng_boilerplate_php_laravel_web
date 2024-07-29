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
    public function update(Request $request, $id)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'template' => 'string',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input data', 'message' => $validator->errors()], 400);
        }

        // Find the email template by id
        $template = EmailTemplate::find($id);

        if (!$template) {
            return response()->json(['error' => 'Template not found', 'message' => 'The email template does not exist'], 404);
        }

        // Update the email template
        $template->update($request->only('title', 'template', 'status'));

        return response()->json([
            'status_code' => 200,
            'message' => 'Email template updated successfully',
            'data' => $template
        ], 200);
    }
}
