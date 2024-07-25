<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Authenticatable  implements JWTSubject, CanResetPasswordContract
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded  = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

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
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class, 'organisation_user', 'user_id', 'org_id')->using(OrganisationUser::class);
    }

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_user')->using(JobUser::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'user_id', 'id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'users_permissions', 'user_id', 'permission_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_id');
    }

    public function hasPermissionTo($permission)
    {
        return $this->permissions()->where('name', $permission)->exists() ||
               $this->roles()->whereHas('permissions', function ($query) use ($permission) {
                   $query->where('name', $permission);
               })->exists();
    }

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function givePermissionTo($permission)
    {
        $this->permissions()->attach($permission);
    }

    public function revokePermissionTo($permission)
    {
        $this->permissions()->detach($permission);
    }

    public function assignRole($role)
    {
        $this->roles()->attach($role);
    }

    public function removeRole($role)
    {
        $this->roles()->detach($role);
    }

    public function getAllPermissions()
    {
        $rolePermissions = $this->roles->load('permissions')->pluck('permissions')->flatten();
        return $this->permissions->merge($rolePermissions)->unique('id');
    }

    public function hasPermissionThroughRole($permission)
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('name', $permission);
        })->exists();
    }

    public function hasAnyRole($roles)
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function hasAnyPermission($permissions)
    {
        return $this->permissions()->whereIn('name', $permissions)->exists() ||
            $this->roles()->whereHas('permissions', function ($query) use ($permissions) {
                $query->whereIn('name', $permissions);
            })->exists();
    }

    public function preferences(): HasMany
    {
        return $this->hasMany(Preference::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

}
