<?php

namespace App\Http\Controllers\Api\V1\Testimonial;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestimonialRequest;
use App\Http\Requests\UpdateTestimonialRequest;
use App\Models\Testimonial;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class TestimonialController extends Controller
{
    public function index()
{
    $this->authorize('viewAny', Testimonial::class);
    
    try {
        $testimonials = Testimonial::all();
        return ResponseHelper::response('Testimonials fetched successfully', Response::HTTP_OK, collect($testimonials));
    } catch (\Exception $e) {
        return ResponseHelper::response('Internal Server Error. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]);
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
    
            return ResponseHelper::response('Testimonial created successfully', Response::HTTP_CREATED, $testimonial->toArray());
        } catch (\Exception $e) {
            return ResponseHelper::response('Internal Server Error. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]);
        }
    }

    public function show($id) 
    {
        try {
            $testimonial = Testimonial::findOrFail($id);
            $this->authorize('view', $testimonial);
            
            return ResponseHelper::response('Testimonial fetched successfully', Response::HTTP_OK, $testimonial->toArray());
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::response('Testimonial not found.', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ResponseHelper::response('Internal Server Error. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]);
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

            return ResponseHelper::response('Testimonial updated successfully', Response::HTTP_OK, $testimonial->toArray());
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::response('Testimonial not found.', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ResponseHelper::response('Internal Server Error. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id) 
    {
        try {
            $testimonial = Testimonial::findOrFail($id);
            $this->authorize('delete', $testimonial);

            $testimonial->delete();
            return ResponseHelper::response('Testimonial deleted successfully', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::response('Testimonial not found.', Response::HTTP_NOT_FOUND);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return ResponseHelper::response('You do not have the required permissions to perform this action.', Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return ResponseHelper::response('Internal Server Error. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR, ['error' => $e->getMessage()]);
        }
    }
}