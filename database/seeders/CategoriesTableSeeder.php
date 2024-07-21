<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

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
        Category::truncate();

        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
                'slug' => 'electronics',
                'parent_id' => null
            ],
            [
                'name' => 'Clothing',
                'description' => 'Apparel and fashion items',
                'slug' => 'clothing',
                'parent_id' => null
            ],
            [
                'name' => 'Home',
                'description' => 'Home and kitchen items',
                'slug' => 'home',
                'parent_id' => null
            ],
            [
                'name' => 'Books',
                'description' => 'Various types of books',
                'slug' => 'books',
                'parent_id' => null
            ],
            [
                'name' => 'Sports',
                'description' => 'Sports and outdoor equipment',
                'slug' => 'sports',
                'parent_id' => null
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

