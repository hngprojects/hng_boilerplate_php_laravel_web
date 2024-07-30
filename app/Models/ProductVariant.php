<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory, HasUuids;

    // Set the key type to string
    protected $keyType = 'string';

    // Disable auto-incrementing IDs
    public $incrementing = false;

    protected $guarded = [];


    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
    
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function productVariantsSize(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'product_variants_size', 'product_variant_id', 'size_id');
    }
}
