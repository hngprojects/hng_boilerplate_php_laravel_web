<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SqueezePageUser extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'squeeze_pages_user';

    protected $guarded = [];

    protected $keyType = 'string';
}
