<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitlistUser extends Model
{
    use HasFactory;

    // Connected table
    protected $table = 'waitlist_users';

    // Mass fillable fields
    protected $fillable = [
        'email',
        'full_name'
    ];
}
