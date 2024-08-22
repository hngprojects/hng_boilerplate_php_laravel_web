<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SqueezePage extends Model
{
    use HasFactory, HasUuids;


    protected $fillable = [
        'title',
        'slug',
        'status',
        'activate',
        'headline',
        'sub_headline',
        'hero_image',
        'content',
    ];
}
