<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiStatus extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'api_group',
        'method',
        'status',
        'response_time',
        'last_checked',
        'details'
    ];
}
