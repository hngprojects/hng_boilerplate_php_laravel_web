<?php

namespace App\Http\Controllers\Api\V1\Testimonial;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestimonialRequest;
use App\Http\Requests\UpdateTestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class TestimonialController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTestimonialRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'Unauthorized. Please log in.',
                'status_code' => 401,
            ], 401);
        }

        try {
            $testimonial = Testimonial::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'content' => $request->content,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Testimonial created successfully',
                'data' => $testimonial,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Internal Server Error',
                'message' => 'Internal Server Error. Please try again later.',
                'status_code' => 500,
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */


    // public function show(Testimonial $testimonial_id)
    // {
    //     $user = Auth::user();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => 'Unauthorized',
    //             'message' => 'Unauthorized. Please log in.',
    //             'status_code' => 401,
    //         ], 401);
    //     }

    //     $testimonial = Testimonial::find($testimonial_id);

    //     if (!$testimonial) {
    //         return response()->json([
    //             'status' => 'Not Found',
    //             'message' => 'Testimonial not found.',
    //             'status_code' => 404,
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Testimonial fetched successfully',
    //         'data' => $testimonial,
    //     ], 200);
    // }

    //     public function show(Testimonial $testimonial)
    // {
    //     $user = Auth::user();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => 'Unauthorized',
    //             'message' => 'Unauthorized. Please log in.',
    //             'status_code' => 401,
    //         ], 401);
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Testimonial fetched successfully',
    //         'data' => $testimonial,
    //     ], 200);
    // }


    public function show($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'Unauthorized. Please log in.',
                'status_code' => 401,
            ], 401);
        }

        try {
            $testimonial = Testimonial::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Testimonial not found.',
                'status_code' => 404,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Testimonial fetched successfully',
            'data' => $testimonial,
        ], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Testimonial $testimonial)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTestimonialRequest $request, string $testimonial_id)
    {
        // Get the authenticated user
        $user = Auth::user();
        // Get testimonial
        $testimonial = Testimonial::find($testimonial_id);
        if (!$testimonial) {
            return response()->json([
                'message' => 'Not found',
                'status_code' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        }

        // Check if testimonial belongs to user
        if ($testimonial->user_id !== $user->id) {
            return response()->json([
                'message' => 'Not authorized to perfom this action',
                'status_code' => Response::HTTP_FORBIDDEN,
            ], Response::HTTP_FORBIDDEN);
        }

        //  update testimonial
        $testimonial->update($request->validated());

        return response()->json([
            'message' => 'Testimonial updated successfully',
            'status_code' => Response::HTTP_OK,
            'data' => $testimonial
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimonial $testimonial)
    {
        //
    }
}
