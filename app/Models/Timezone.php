<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Timezone extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $keyType = 'string';

    public $incrementing = false;
    protected $casts = [
        'gmtoffset' => 'string',
        'timezone' => 'string',
        'description' => 'string',
    ];
    

    public function preferences()
    {
        return $this->hasMany(Preference::class);
    }
}
 