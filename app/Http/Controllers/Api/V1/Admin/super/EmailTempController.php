<?php

namespace App\Http\Controllers\Api\V1\Admin\super;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return EmailTemplate::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'template' => 'required|string',
            'status' => 'required|boolean',
        ]);

        $emailTemplate = EmailTemplate::create($request->all());
        return response()->json($emailTemplate, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return EmailTemplate::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'template' => 'required|string',
            'status' => 'required|boolean',
        ]);

        $emailTemplate = EmailTemplate::findOrFail($id);
        $emailTemplate->update($request->all());

        return response()->json($emailTemplate, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        EmailTemplate::findOrFail($id)->delete();
    return response()->json(null, 204);
    }
}
