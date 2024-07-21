<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = ['link', 'user_id', 'org_id', 'expires_at'];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
