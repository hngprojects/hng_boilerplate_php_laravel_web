<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::all()->each(function ($product) {
            ProductVariant::factory()->count(3)->create([
                'product_id' => $product->product_id,
            ]);
        });
    }
}
