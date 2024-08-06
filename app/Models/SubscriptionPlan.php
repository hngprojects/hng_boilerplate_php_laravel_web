<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'price', 'duration', 'description'];

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'features_subscription_plans')
            ->withPivot('status')
            ->withTimestamps();
    }

}
