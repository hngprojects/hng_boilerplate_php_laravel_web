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

    // protected static function boot()
    // {
    //     parent::boot();
    //     self::creating(function ($org) {
    //         $org->slug = $org->uniqueSlug($org->name);
    //     });
    // }

    // public function uniqueSlug($name){
    //     $slug = Str::slug($name);
    //     $count=1;
    //     $initialSlug = $slug;
    //     while(static::where('slug', $slug)->exists()){
    //       $slug = $initialSlug."-".$count;
    //       $count++;
    //     }
    //     return $slug;
    // }

    public function users()
    {
        return $this->belongsToMany(User::class, 'organisation_user', 'org_id', 'user_id')->using(OrganisationUser::class);
    }

    public function getPublicColumns()
    {
        $publicColumns = ['org_id', "user_id", "name", "slug", "description", "email", "industry", "type", "country", "address", "state" ];
        return $this->only($publicColumns);
    }

}
