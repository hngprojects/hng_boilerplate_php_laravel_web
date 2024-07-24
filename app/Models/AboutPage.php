<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    use HasFactory;

    // Defining editable field
    protected $fillable = [
        'title', 
        'introduction',
        'years_in_business',
        'customers',
        'monthly_blog_readers',
        'social_followers',
        'services_title',
        'services_description'
    ];
}
