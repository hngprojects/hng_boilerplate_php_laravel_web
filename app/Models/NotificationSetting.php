<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_notifications',
        'sms_notifications',
        'profile_id',
    ];

    /**
     * Get the user that owns the notification setting.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

