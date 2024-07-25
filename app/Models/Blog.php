<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Blog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['title', 'content', 'author'];

    public function tags(): HasMany
    {
        return $this->hasMany(BlogTag::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(BlogImage::class);
    }
}
