<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpArticle extends Model
{
    use HasFactory, HasUuids;

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

    // Define relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
