<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFaqRequest;
use App\Http\Requests\UpdateFaqRequest;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer|min:1',
            'size' => 'nullable|integer|min:1',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input parameters.',
                'errors' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $perPage = $request->input('size', 15);

        $faqs = Faq::where('status', 1)->paginate($perPage);

        return response()->json([
            'status_code' => 200,
            'message' => "Faq returned successfully",
            'data' => collect($faqs->items())->map(function ($faq) {
                return [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                ];
            }),
            'pagination' => [
                'current_page' => $faqs->currentPage(),
                'total_pages' => $faqs->lastPage(),
                'page_size' => $faqs->perPage(),
                'total_items' => $faqs->total(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateFaqRequest $request)
    {
        try {
            $data = $request->validated();

            $faq = Faq::create($data);

            return response()->json([
                'status_code' => Response::HTTP_CREATED,
                'message' => "The FAQ has been successfully created.",
                'data' => [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'category' => $faq->category,
                    'createdBy' => "",
                    'createdAt' => $faq->created_at,
                    'updatedAt' => $faq->updated_at,
                ]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Faq $faq)
    {
        return response()->json([
            'status_code' => 200,
            'message' => "Faq returned successfully",
            'data' => $faq
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFaqRequest $request, Faq $faq)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'status_code' => Response::HTTP_FORBIDDEN,
                'message' => 'Only admin users can update a faq',
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $data = $request->validated();

            $faq->update($data);

            return response()->json([
                'status_code' => Response::HTTP_CREATED,
                'message' => "The FAQ has been updated created.",
                'data' => [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'category' => $faq->category,
                    'createdBy' => "",
                    'createdAt' => $faq->created_at,
                    'updatedAt' => $faq->updated_at,
                ]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($faq)
    {
        $faq = Faq::find($faq);

        if (!$faq) {
            return response()->json([
                'code' => 400,
                'description' => 'Bad Request.',
                'links' => []
            ], 400);
        }

        $faq->delete();

        return response()->json([
            'code' => 200,
            'description' => 'The FAQ has been successfully deleted.',
            'links' => []
        ], 200);
    }
}
