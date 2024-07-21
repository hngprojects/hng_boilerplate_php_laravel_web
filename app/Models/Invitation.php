<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory, HasUuids;
    protected $primaryKey = 'invite_id'; // Specify primary key column
    public $incrementing = false; // Indicates the ID is not auto-incrementing
    protected $keyType = 'string'; // Indicates the primary key type is string
    protected $fillable = ['link', 'email', 'org_id', 'expires_at'];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
