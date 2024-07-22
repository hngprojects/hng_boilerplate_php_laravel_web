<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;


class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition()
    {
        return [
            'article_id' => Str::uuid(),
            'user_id' => User::factory(),
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(3, true),
        ];
    }
}
