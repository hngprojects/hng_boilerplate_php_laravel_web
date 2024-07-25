<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AboutPage;


class AboutPageController extends Controller
{
    public function show(Request $request)
    {
        try {
            // Check if the user is an admin
            if (!$request->user() || !$request->user()->isAdmin()) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'status_code' => 401
                ], 401);
            }

            // Retrieve the About page content
            $aboutPage = AboutPage::first();
            
            if (!$aboutPage) {
                return response()->json([
                    'message' => 'Failed to retrieve About page content.',
                    'status_code' => 500
                ], 500);
            }

            return response()->json([
                'title' => $aboutPage->title,
                'introduction' => $aboutPage->introduction,
                'custom_sections' => [
                    'stats' => [
                        'years_in_business' => $aboutPage->years_in_business,
                        'customers' => $aboutPage->customers,
                        'monthly_blog_readers' => $aboutPage->monthly_blog_readers,
                        'social_followers' => $aboutPage->social_followers
                    ],
                    'services' => [
                        'title' => $aboutPage->services_title,
                        'description' => $aboutPage->services_description
                    ]
                ],
                'status_code' => 200,
                'message' => 'Retrieved about page content successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve About page content.',
                'status_code' => 500
            ], 500);
        }
    }
}
