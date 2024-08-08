<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Organisation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        "name",
        "user_id",
        "email",
        "description",
        "industry",
        "type",
        "country",
        "address",
        "state",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['pivot'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'org_id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    public function users()
    {
        return $this->belongsToMany(User::class, 'organisation_user', 'org_id', 'user_id')->using(OrganisationUser::class);
    }

    // Define the inverse relationship
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getPublicColumns()
    {
        $publicColumns = ['org_id', "user_id", "name", "slug", "description", "email", "industry", "type", "country", "address", "state"];
        return $this->only($publicColumns);
    }
    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function roles()
    {
        return $this->hasMany(Role::class, 'org_id');
    }

    public function subscription()
    {
        return $this->hasOne(UserSubscription::class, 'org_id', 'org_id');
    }

    public function subscriptionPlan()
    {
        return $this->hasOneThrough(
            SubscriptionPlan::class,
            UserSubscription::class,
            'org_id', // Foreign key on UserSubscription table
            'id',     // Foreign key on SubscriptionPlan table
            'org_id', // Local key on Organisation table
            'subscription_plan_id' // Local key on UserSubscription table
        );
    }
}
