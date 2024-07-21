<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Squeeze extends Model
{
    use HasFactory;

    protected $fillable = [
        'email', 'first_name', 'last_name', 'phone', 'location',
        'job_title', 'company', 'interests', 'referral_source'
    ];

    protected $casts = [
        'interests' => 'array',
    ];
}
