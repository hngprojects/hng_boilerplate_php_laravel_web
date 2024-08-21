<?php

namespace Database\Seeders;

use App\Models\SqueezePage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class SqueezePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {

        $squeezePages = [
            [
                'title' => 'Digital Marketing',
                'slug' => 'digital-marketing',
                'status' => 'online',
                'activate' => true,
                'headline' => 'Master Digital Marketing',
                'sub_headline' => 'Unlock the Secrets of Online Success',
                'hero_image' => 'digital_marketing.jpg',
                'content' => 'Learn the best strategies to excel in digital marketing...',
            ],
            [
                'title' => 'Conversion Secrets',
                'slug' => 'conversion-secrets',
                'status' => 'online',
                'activate' => true,
                'headline' => 'Increase Your Conversions',
                'sub_headline' => 'Discover Proven Techniques',
                'hero_image' => 'conversion_secrets.jpg',
                'content' => 'Find out how to turn visitors into customers...',
            ],
            [
                'title' => 'Email Mastery',
                'slug' => 'email-mastery',
                'status' => 'offline',
                'activate' => false,
                'headline' => 'Master Email Marketing',
                'sub_headline' => 'Boost Your Campaigns',
                'hero_image' => 'email_mastery.jpg',
                'content' => 'Effective email marketing strategies to engage your audience...',
            ],
            [
                'title' => 'Social Growth',
                'slug' => 'social-growth',
                'status' => 'offline',
                'activate' => false,
                'headline' => 'Grow Your Social Media',
                'sub_headline' => 'Strategies for Success',
                'hero_image' => 'social_growth.jpg',
                'content' => 'Learn how to grow your social media presence...',
            ],
            [
                'title' => 'Content Blueprint',
                'slug' => 'content-blueprint',
                'status' => 'online',
                'activate' => true,
                'headline' => 'Content Marketing Blueprint',
                'sub_headline' => 'Your Guide to Success',
                'hero_image' => 'content_blueprint.jpg',
                'content' => 'Step-by-step guide to successful content marketing...',
            ],
        ];

        foreach ($squeezePages as $squeezePage) {
            SqueezePage::create($squeezePage);
        }
    }
}
