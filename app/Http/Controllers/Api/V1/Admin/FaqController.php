<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $faq = Faq::where('status', 1)->get();

        return response()->json([
            'status_code' => 200,
            'message' => "Faq returned successfully",
            'data' => $faq
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:500',
        ]);

        $faq = Faq::create($data);

        return response()->json([
            'status_code' => 201,
            'message' => "Faq created successfully",
            'data' => $faq
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question' => 'sometimes|string|max:255',
            'answer' => 'sometimes|string|max:500',
        ]);

        $updateFaq = $faq->update($data);

        return response()->json([
            'status_code' => 200,
            'message' => "Faq updated successfully",
            'data' => $faq
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        return response()->noContent();
    }
}
