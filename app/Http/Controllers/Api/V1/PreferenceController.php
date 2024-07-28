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
        $preferences = Auth::user()->preferences;

        Log::info('Preferences retrieved', ['user_id' => Auth::id(), 'preferences' => $preferences]);

        return response()->json(['preferences' => $preferences ?? []], 200);
    }

    //
    public function store(StorePreferenceRequest $request)
    {
        try {
            $preference = Auth::user()->preferences()->create($request->all());

            Log::info('Preference created', ['user_id' => Auth::id(), 'preference' => $preference]);

            return response()->json([
                'id' => $preference->id,
                'name' => $preference->name,
                'value' => $preference->value,
                'message' => 'Preference created successfully.'
            ], 201);

        } catch (\Exception $e) {
            Log::error("Error creating: $e");
            return response()->json([
                'error' => 'Bad Request',
                "message" => "Internal server error",
                "status_code" => 500
            ], 500);
        }
    }

    public function update(UpdatePreferenceRequest $request, $id)
    {
        try {
            $preference = Auth::user()->preferences()->findOrFail($id);
            $preference->update($request->only(['name', 'value']));

            Log::info('Preference updated', ['user_id' => Auth::id(), 'preference' => $preference]);

            return response()->json([
                'id' => $preference->id,
                'name' => $preference->name,
                'value' => $preference->value,
                'message' => 'Preference updated successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error updating: $e");
            return response()->json([
                'error' => 'Bad Request',
                "message" => "Internal server error",
                "status_code" => 500
            ], 500);
        }
    }

    public function delete(DeletePreferenceRequest $request, $id)
    {
        $preference = Auth::user()->preferences()->find($id);

        if (!$preference) {
            return response()->json(['error' => 'Preference not found.'], 404);
        }

        $preference->delete();

        Log::info('Preference deleted', ['user_id' => Auth::id(), 'preference_id' => $id]);

        return response()->json(['message' => 'Preference deleted successfully.'], 200);
    }
}
