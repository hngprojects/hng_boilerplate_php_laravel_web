<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CookiePreference extends Model
{
    use HasFactory;

    // Set the table name if it's not the plural form of the model name
    protected $table = 'cookie_preferences';

    // The attributes that are mass assignable.
    protected $fillable = ['id', 'user_id', 'preferences'];

    // Cast attributes to the desired data type
    protected $casts = [
        'preferences' => 'array', // Cast the preferences JSON field to an array
    ];

    // Indicates if the model's ID is auto-incrementing
    public $incrementing = false;

    // Indicates the type of the model's primary key (UUID)
    protected $keyType = 'string';
}
