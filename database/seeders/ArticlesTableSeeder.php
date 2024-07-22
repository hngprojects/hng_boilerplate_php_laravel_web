<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Article;


class ArticlesTableSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        foreach ($this->articles as $article) {
            Article::create([
                'user_id' => $users->random()->id,
                'title' => $article['title'],
                'content' => $article['content'],
            ]);
        }
    }

    protected $articles = [
        [
            'title' => 'How to reset your password',
            'content' => 'To reset your password, follow these steps...'
        ],
        [
            'title' => 'How to update your profile',
            'content' => 'To update your profile, go to the settings page...'
        ],
        [
            'title' => 'How to delete your account',
            'content' => 'To delete your account, please contact support...'
        ]
    ];
}
