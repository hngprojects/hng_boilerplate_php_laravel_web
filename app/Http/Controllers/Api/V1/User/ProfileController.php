<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; 
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'job_title' => 'string|max:255|nullable',
            'pronoun' => 'string|max:255|nullable',
            'bio' => 'string|max:500|nullable',
            'email' => 'string|email|max:255|nullable',
            'avatar_url' => 'string|nullable', 
            'display_image' => 'string|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['Status' => 400, 'Message' => $validator->errors()], 400);
        }

        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return response()
            ->json([
                'Status' => 404, 
                'Message' => 'Profile not found'
            ], 404);
        }

        $profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'job_title' => $request->job_title,
            'pronoun' => $request->pronoun,
            'bio' => $request->bio,
            'avatar_url' => $request->avatar_url,
            'display_image' => $request->display_image,
        ]);

        $user_profile = User::findOrFail($profile->user_id);

        if ($request->has('email')) {
            $user_profile->update(['email' => $request->email]);
        }

        return response()
        ->json([
            'Status' => 200, 
            'Message' => 'Profile updated successfully', 
            'Data' => $profile->refresh()
        ], 200);
    }



    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()
            ->json([
                'Status' => 400, 
                'Message' => $validator->errors()
            ], 400);
        }

        $file = $request->file('file');
        $imagePath = $file->getRealPath();

        try {
            $response = Http::post('https://api.cloudinary.com/v1_1/' . env('CLOUDINARY_CLOUD_NAME') . '/image/upload', [
                'file' => base64_encode(file_get_contents($imagePath)),
                'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),
            ]);

            $data = $response->json();

            if ($response->successful()) {
                $uploadedFileUrl = $data['secure_url'];

                $user = Auth::user();
                $profile = $user->profile;

                if (!$profile) {
                    return response()
                    ->json([
                        'Status' => 404, 
                        'Message' => 'Profile not found'
                    ], 404);
                }

                $profile->update(['avatar_url' => $uploadedFileUrl]);

                return response()
                ->json([
                    'Status' => 200, 
                    'Message' => 'Image uploaded and profile updated successfully', 
                    'Data' => ['avatar_url' => $uploadedFileUrl]
                ], 200);
            } else {
                return response()->json(['Status' => 500, 'Message' => 'Failed to upload image'], 500);
            }
        }  catch (\Exception $e) {
           return response()
           ->json([
             'Status' => 500, 
             'Message' => 'Failed to upload image'
           ], 500);
       }

    }


    /**
     * updatePassword
     */
    public function updatePassword(Request $request)
{
    // Validation
    $request->validate([
        'old_password' => 'required',
        'new_password' => [
            'required',
            'confirmed',
            'min:8', // at least 8 characters
            'regex:/[A-Z]/', // at least one uppercase letter
            'regex:/[0-9]/' // at least one number
        ],
    ]);

    if (!Hash::check($request->old_password, Auth::user()->password)) {
        return response()->json([
            'Status' => 400,
            'message' => 'Old Password does not match!',
        ], 400);
    }

    // Update The New Password
    User::whereId(Auth::user()->id)->update([
        'password' => Hash::make($request->new_password)
    ]);

    return response()->json([
        'Status' => 200,
        'Message' => 'Password changed successfully',
    ], 200);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
