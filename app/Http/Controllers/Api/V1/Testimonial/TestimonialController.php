<?php

namespace App\Http\Controllers\Api\V1\Testimonial;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestimonialRequest;
use App\Http\Requests\UpdateTestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
    public function show(Testimonial $testimonial)
    {
        //
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
    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimonial $testimonial)
    {
        //
    }
}
