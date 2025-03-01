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

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $this->authorize('viewAny', Testimonial::class);
        
        try {
            $testimonials = Testimonial::all();
            return response()->json($this->successResponse('Testimonials fetched successfully', $testimonials->toArray()));
        } catch (\Exception $e) {
            return response()->json($this->errorResponse('Internal Server Error. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]));
        }
    }

    public function store(StoreTestimonialRequest $request)
    {
        $this->authorize('create', Testimonial::class);
        
        try {
            $user = Auth::user();
            $name = $request->get('name') ?? $user->name; 
            if (empty($name)) {
                $name = 'Anonymous User'; 
            }

            $testimonial = Testimonial::create([
                'user_id' => $user->id,
                'name' => $name,
                'content' => $request->get('content'),
            ]);
    
            return response()->json($this->successResponse('Testimonial created successfully', $testimonial->toArray()), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json($this->errorResponse('Internal Server Error. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]));
        }
    }

    public function show($id) 
    {
        try {
            $testimonial = Testimonial::findOrFail($id);
            $this->authorize('view', $testimonial);
            
            return response()->json($this->successResponse('Testimonial fetched successfully', $testimonial->toArray()));
        } catch (ModelNotFoundException $e) {
            return response()->json($this->errorResponse('Testimonial not found.', Response::HTTP_NOT_FOUND));
        } catch (\Exception $e) {
            return response()->json($this->errorResponse('Internal Server Error. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]));
        }
    }

    public function update(UpdateTestimonialRequest $request, $id) 
    {
        try {
            $testimonial = Testimonial::findOrFail($id);
            $this->authorize('update', $testimonial);

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

    public function destroy($id) 
{
    try {
        $testimonial = Testimonial::findOrFail($id);
        $this->authorize('delete', $testimonial);

        $testimonial->delete();
        return response()->json($this->successResponse('Testimonial deleted successfully'));
    } catch (ModelNotFoundException $e) {
        return response()->json($this->errorResponse('Testimonial not found.', Response::HTTP_NOT_FOUND));
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        
        return response()->json($this->errorResponse('You do not have the required permissions to perform this action.', Response::HTTP_FORBIDDEN));
    } catch (\Exception $e) {
        return response()->json($this->errorResponse('Internal Server Error. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]));
    }
}
}