<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Blog::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = ['Business', 'Food', 'Lifestyle', 'World News'];
        $images = [
            'https://free-images.com/lg/dd2c/port_au_prince_haiti.jpg',
            'https://free-images.com/lg/45e3/squirrel_tree_mammal_paw.jpg',
            'https://free-images.com/lg/556a/netherlands_landscape_sky_clouds.jpg',
            'https://free-images.com/lg/4275/penguin_funny_blue_water.jpg'
        ];

        foreach($categories as $key => $category) {
            $imageContents = file_get_contents($images[$key]);
            $imageName = Str::random(10) . '.jpg';
            Storage::disk('public')->put('images/' . $imageName, $imageContents);
            $imagePath = 'storage/images/' . $imageName;

            Blog::factory()->create(['category' => $category, 'image_url' => $imagePath]);
        }
    }
}
