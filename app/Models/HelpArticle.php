<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpArticle extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'content', 'category'];

    // Define relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
