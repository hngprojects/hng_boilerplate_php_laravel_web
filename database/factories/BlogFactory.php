<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    protected $model = Blog::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $blog_category = BlogCategory::factory()->create();
        $blog_id = Str::uuid();
        return [
            'id' => $blog_id,
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(3, true),
            'author' => $this->faker->name,
            'blog_category_id' => $blog_category->id
        ];
    }
}
