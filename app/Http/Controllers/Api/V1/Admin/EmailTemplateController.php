<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class EmailTemplateController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Validate UUID format
        if (!\Illuminate\Support\Str::isUuid($id)) {
            return response()->json(['error' => 'Invalid template ID format'], Response::HTTP_BAD_REQUEST);
        }

        // Retrieve the email template
        $template = EmailTemplate::find($id);

        if (!$template) {
            return response()->json(['error' => 'Template not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($template, Response::HTTP_OK);
    }
    
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

    public function destroy($id)
{
    // Find the email template by ID
    $emailTemplate = EmailTemplate::find($id);

    // Check if the email template exists
    if (!$emailTemplate) {
        return response()->json([
            'status_code' => 404,
            'error' => 'Not Found',
            'message' => 'Email template not found'
        ], 404);
    }

    // Delete the email template
    $emailTemplate->delete();

    // Return success response
    return response()->json([
        'status_code' => 200,
        'message' => 'Email template deleted successfully'
    ], 200);
}
}
