<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['subscription_plan_id', 'cancellation_reason'];
}
