<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Blog extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['title', 'content', 'author', 'blog_category_id'];

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    public function blog_category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(BlogImage::class);
    }

    public function image(): HasOne
    {
        return $this->hasOne(BlogImage::class);
    }
}
