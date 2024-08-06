<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductVariant;
use App\Models\ProductVariantSize;

class ProductVariantSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductVariant::all()->each(function ($variant) {
            ProductVariantSize::factory()->count(2)->create([
                'product_variant_id' => $variant->id,
            ]);
        });
    }
}
