<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Preference extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    // Set the key type to string
    protected $keyType = 'string';

    // Disable auto-incrementing IDs
    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }
}
