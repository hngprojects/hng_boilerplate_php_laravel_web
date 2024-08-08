<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSqueezePageRequest;
use App\Models\SqueezePage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SqueezePageCoontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $squezee_pages = SqueezePage::select(['id', 'title', 'slug', 'created_at', 'status', 'activate'])->get();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Squeeze Pages retrieved successfully',
                'data' => $squezee_pages,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSqueezePageRequest $request)
    {
        try {
            $validated = $request->validate();

            $squeeze_page = SqueezePage::create([$validated]);

            return response()->json([
                'status_code' => Response::HTTP_OK,
                'message' => 'Squeeze page created successfully',
                'data' => [
                    'id' => $squeeze_page->id,
                    'title' => $squeeze_page->title,
                    'slug' => $squeeze_page->slug,
                    'created_at' => $squeeze_page->created_at,
                    'status' => $squeeze_page->status,
                    'activate' => $squeeze_page->activate
                ]
            ], Response::HTTP_OK);

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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
