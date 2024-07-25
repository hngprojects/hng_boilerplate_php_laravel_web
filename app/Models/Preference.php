<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Concerns\HasUuids;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Preference extends Model
// {
//     use HasFactory, HasUuids;

//     public function user()
//     {
//         return $this->belongsTo(User::class);
//     }
// }

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Preference extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $fillable = ['name', 'value', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
