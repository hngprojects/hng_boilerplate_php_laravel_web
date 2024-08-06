<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HelpArticle extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'articles';

    // Define the primary key
    protected $primaryKey = 'article_id';

    // Define the key type
    protected $keyType = 'string';

    // Disable auto-incrementing as we're using UUIDs
    public $incrementing = false;

    // Define the fillable fields
    protected $fillable = ['user_id', 'title', 'content'];

    // Define the boot method to set UUID on creation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->article_id)) {
                $model->article_id = (string) Str::uuid();
            }
        });
    }

    // Define relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
