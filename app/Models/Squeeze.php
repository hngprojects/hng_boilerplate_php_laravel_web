<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Squeeze extends Model
{
    use HasFactory;

    // Set the key type to string
    protected $keyType = 'string';

    // Disable auto-incrementing IDs
    public $incrementing = false;

    protected $fillable = [
        'email', 'first_name', 'last_name', 'phone', 'location',
        'job_title', 'company', 'interests', 'referral_source'
    ];

    protected $casts = [
        'interests' => 'array',
    ];

    // Automatically generate a UUID when creating a new instance
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
