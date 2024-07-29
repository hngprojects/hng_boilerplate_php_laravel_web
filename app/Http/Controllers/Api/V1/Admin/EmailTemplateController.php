<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

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
}