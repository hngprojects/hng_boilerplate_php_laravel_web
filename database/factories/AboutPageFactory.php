<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AboutPage>
 */
class AboutPageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'More Than Just A BoilerPlate',
            'introduction' => 'Welcome to Hng Boilerplate, where passion meets innovation.',
            'years_in_business' => rand(1, 10),
            'customers' => rand(10000, 1000000),
            'monthly_blog_readers' => 100000,
            'social_followers' => 1200000,
            'services_title' => 'Trained to Give You The Best',
            'services_description' => 'Since our founding, Hng Boilerplate has been dedicated to constantly evolving to stay ahead of the curve.',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
