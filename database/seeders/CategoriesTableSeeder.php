<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table to remove existing records
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        // Define categories
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
                'slug' => 'electronics',
            ],
            [
                'name' => 'Clothing',
                'description' => 'Apparel and fashion items',
                'slug' => 'clothing',
            ],
            [
                'name' => 'Home',
                'description' => 'Home and kitchen items',
                'slug' => 'home',
            ],
            [
                'name' => 'Books',
                'description' => 'Various types of books',
                'slug' => 'books',
            ],
            [
                'name' => 'Sports',
                'description' => 'Sports and outdoor equipment',
                'slug' => 'sports',
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

