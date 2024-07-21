<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['feature', 'description'];

    public function subscriptions_plans(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'features_subscription_plans')
            ->withPivot('status')
            ->withTimestamps();
    }
}
