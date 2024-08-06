<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        $author = User::factory()->create(['role' => 'admin']);
        $image = UploadedFile::fake()->image('image1.jpg');
        $path = Storage::putFile('public/images', $image);
        $blog_id = Str::uuid();
        return [
            'id' => $blog_id,
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(3, true),
            'author' => $author->name,
            'author_id' => $author->id,
            'category' => $this->faker->word(),
            'image_url' => $path
        ];
    }
}
