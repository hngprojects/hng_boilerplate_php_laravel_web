<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, HasUuids;
    protected static function booted()
    {
        static::creating(function ($article) {
            $article->article_id = (string) Str::uuid();
        });
    }
    protected $primaryKey = 'article_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['user_id', 'title', 'content'];
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
