<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Size;
use App\Models\ProductVariantSize;
use App\Models\ProductVariant;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductVariantSizeFactory extends Factory
{
    protected $model = ProductVariantSize::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'size_id' => Size::factory(),
            'product_variant_id' => ProductVariant::factory(),
        ];
    }
}
