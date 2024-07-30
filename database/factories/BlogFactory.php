<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
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
        return [
            'id' => Str::uuid(),
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(3, true),
            'author' => $this->faker->name,
            'blog_category_id' => $blog_category->id,
        ];
    }
}
