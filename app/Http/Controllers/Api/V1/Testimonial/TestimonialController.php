<?php

namespace App\Http\Controllers\Api\V1\Testimonial;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestimonialRequest;
use App\Http\Requests\UpdateTestimonialRequest;
use App\Models\Testimonial;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class TestimonialController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json($this->errorResponse('Unauthorized. Please log in.', Response::HTTP_UNAUTHORIZED));
        }

        try {
            $testimonials = Testimonial::all();
            return response()->json($this->successResponse('Testimonials fetched successfully', $testimonials->toArray()));
        } catch (\Exception $e) {
            return response()->json($this->errorResponse('Internal Server Error. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTestimonialRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json($this->errorResponse('Unauthorized. Please log in.', Response::HTTP_UNAUTHORIZED));
        }

        try {
            // Check if user has a name, if not use a fallback
            $userName = $user->name ?? $user->username ?? 'Anonymous User';

            $testimonial = Testimonial::create([
                'user_id' => $user->id,
                'name' => $userName,
                'content' => $request->get('content'),
            ]);

            return response()->json($this->successResponse('Testimonial created successfully', $testimonial->toArray()), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json($this->errorResponse('Internal Server Error. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) 
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json($this->errorResponse('Unauthorized. Please log in.', Response::HTTP_UNAUTHORIZED));
        }

        try {
            $testimonial = Testimonial::findOrFail($id);
            return response()->json($this->successResponse('Testimonial fetched successfully', $testimonial->toArray()));
        } catch (ModelNotFoundException $e) {
            return response()->json($this->errorResponse('Testimonial not found.', Response::HTTP_NOT_FOUND));
        } catch (\Exception $e) {
            return response()->json($this->errorResponse('Internal Server Error. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTestimonialRequest $request, string $id) 
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json($this->errorResponse('Unauthorized. Please log in.', Response::HTTP_UNAUTHORIZED));
        }

        try {
            $testimonial = Testimonial::findOrFail($id);

            // Check if the user owns this testimonial or is an admin
            if ($testimonial->user_id !== $user->id && $user->role !== 'admin') {
                return response()->json($this->errorResponse('You do not have permission to update this testimonial.', Response::HTTP_FORBIDDEN));
            }

            $testimonial->update([
                'content' => $request->get('content')
            ]);

            return response()->json($this->successResponse('Testimonial updated successfully', $testimonial->toArray()));
        } catch (ModelNotFoundException $e) {
            return response()->json($this->errorResponse('Testimonial not found.', Response::HTTP_NOT_FOUND));
        } catch (\Exception $e) {
            return response()->json($this->errorResponse('Internal Server Error. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) 
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json($this->errorResponse('Unauthorized. Please log in.', Response::HTTP_UNAUTHORIZED));
        }

        if ($user->role !== 'admin') {
            return response()->json($this->errorResponse('You do not have the required permissions to perform this action.', Response::HTTP_FORBIDDEN));
        }

        try {
            $testimonial = Testimonial::findOrFail($id);
            $testimonial->delete();

            return response()->json($this->successResponse('Testimonial deleted successfully'));
        } catch (ModelNotFoundException $e) {
            return response()->json($this->errorResponse('Testimonial not found.', Response::HTTP_NOT_FOUND));
        } catch (\Exception $e) {
            return response()->json($this->errorResponse('Internal Server Error. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]));
        }
    }
}