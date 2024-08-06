<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Gateway extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name'];

    protected $casts = [
        'id' => 'uuid',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
