<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    // Set the key type to string
    protected $keyType = 'string';

    // Disable auto-incrementing IDs
    public $incrementing = false;

    public function ProductVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function productVariantsSize()
    {
        return $this->hasMany(ProductVariantSize::class);
    }
}
