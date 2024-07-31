<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['type', 'title', 'message'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_notifications')->withPivot('status')
            ->withTimestamps();;
    }
}
