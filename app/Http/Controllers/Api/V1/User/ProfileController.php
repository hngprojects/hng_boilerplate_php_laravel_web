<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Preference;
use App\Models\Profile;
use App\Models\Timezone;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; 
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;


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
            'file' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'Status' => 400,
                'Message' => $validator->errors()
            ], 400);
        }
    
   
        $file = $request->file('file');
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $filePath = public_path('uploads');
        
       
        if (!File::exists($filePath)) {
            File::makeDirectory($filePath, 0755, true);
        }
    
  
        $file->move($filePath, $fileName);
    
       
        $imageUrl = url('uploads/' . $fileName);
    
       
        $user = Auth::user();
        $profile = $user->profile;
    
        if (!$profile) {
            return response()->json([
                'Status' => 404,
                'Message' => 'Profile not found'
            ], 404);
        }
    
        $profile->update(['avatar_url' => $imageUrl]);
    
        return response()->json([
            'Status' => 200,
            'Message' => 'Image uploaded and profile updated successfully',
            'Data' => ['avatar_url' => $imageUrl]
        ], 200);
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






    public function storeTimezones(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'timezone' => 'required|string|unique:timezones,timezone',
            'gmtoffset' => 'required|string',
            'description' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'Status' => 409,
                'Message' => 'Timezone already exists',
                'Errors' => $validator->errors(),
            ], 409);
        }
    
        $timezone = Timezone::create($request->only(['timezone', 'gmtoffset', 'description']));
    
        if (auth()->check()) {
            $userId = auth()->id();
            Preference::updateOrCreate(
                ['user_id' => $userId],
                [
                    'timezone_id' => $timezone->id,
                    'name' => $timezone->timezone,
                    'value' => $timezone->timezone // Ensure the `value` column is populated
                ]
            );
        }
    
        return response()->json([
            'id' => $timezone->id,
            'timezone' => $timezone->timezone,
            'gmtoffset' => $timezone->gmtoffset,
            'description' => $timezone->description,
        ], 201);
    }
    
    





       public function getAllTimezones()
{
    $timezones = Timezone::latest()->get();

    return response()->json([
        'Status' => 200,
        'Message' => 'List of supported timezones.',
        'Data' => $timezones,
    ], 200);
}//End method  







public function updateTimezones(Request $request, $id)
{
    $timezone = Timezone::find($id);

    if (!$timezone) {
        return response()->json([
            'Status' => 404,
            'Message' => 'Timezone not found',
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'timezone' => 'required|string|unique:timezones,timezone,' . $id,
        'gmtoffset' => 'required|string',
        'description' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'Status' => 409,
            'Message' => 'Timezone already exists',
            'Errors' => $validator->errors(),
        ], 409);
    }

    $timezone->update($request->only(['timezone', 'gmtoffset', 'description']));

    if (auth()->check()) {
        $userId = auth()->id();
        Preference::updateOrCreate(
            ['user_id' => $userId],
            [
                'timezone_id' => $timezone->id,
                'name' => $timezone->timezone,
                'value' => $timezone->timezone // Ensure the `value` column is populated
            ]
        );
    }

    return response()->json([
        'id' => $timezone->id,
        'timezone' => $timezone->timezone,
        'gmtoffset' => $timezone->gmtoffset,
        'description' => $timezone->description,
    ], 200);
}



    




}
