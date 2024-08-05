<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Language extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    // Set the key type to string
    protected $keyType = 'string';

    // Disable auto-incrementing IDs
    public $incrementing = false;

    public function preferences()
    {
        return $this->hasMany(Preference::class);
    }
}
