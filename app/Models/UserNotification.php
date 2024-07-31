<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id', 'notification_id', 'status'];

    protected $table = 'user_notifications';
}
