<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Preference\DeletePreferenceRequest;
use App\Http\Requests\Preference\StorePreferenceRequest;
use App\Http\Requests\Preference\UpdatePreferenceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PreferenceController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }

        $preferences = Auth::user()->preferences;

        Log::info('Preferences retrieved', ['user_id' => Auth::id(), 'preferences' => $preferences]);
        return response()->json([
            'status' => 200,
            'message' => 'Languages fetched successfully',
            'preferences' => $preferences
        ], 200);

    }

    //
    

    public function store(StorePreferenceRequest $request){
        if (!auth()->check()) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validated();
        
        if (!$validated) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }
    
        try {
            $preference = Auth::user()->preferences()->create($request->all());
    
            Log::info('Preference created', ['user_id' => Auth::id(), 'preference' => $preference]);
    
            return response()->json([
                'status' => 201,
                'message' => 'Preference created successfully',
                'preference' => [
                    'id' => $preference->id,
                    'name' => $preference->name,
                    'value' => $preference->value,
                ]
            ], 201);
    
        } catch (\Exception $e) {
            Log::error("Error creating preference: {$e->getMessage()}");
    
            return response()->json([
                'status' => 500,
                'message' => 'Internal Server Error',
                'error' => 'Server Error'
            ], 500);
        }
    }

    public function update(UpdatePreferenceRequest $request, $id)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validated();
        
        if (!$validated) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $preference = Auth::user()->preferences()->findOrFail($id);

            if(!$preference) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Preference not found',
                ], 404);
            }
            $preference->update($request->only(['name', 'value']));

            Log::info('Preference updated', ['user_id' => Auth::id(), 'preference' => $preference]);

            return response()->json([
                'status' => 200,
                'message' => 'Preference updated successfully',
                'preference' => [
                    'id' => $preference->id,
                    'name' => $preference->name,
                    'value' => $preference->value,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error updating preference: {$e->getMessage()}");

            return response()->json([
                'status' => 500,
                'message' => 'Internal Server Error',
                'error' => 'Server Error'
            ], 500);
        }
    }
    
    public function delete(DeletePreferenceRequest $request, $id)
    {
        $preference = Auth::user()->preferences()->find($id);

        if (!$preference) {
            return response()->json([
                'status' => 404,
                'error' => 'Preference not found',
            ], 404);
        }

        $preference->delete();

        Log::info('Preference deleted', ['user_id' => Auth::id(), 'preference_id' => $id]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Preference deleted successfully.',
        ], 200);
    }

        
}
