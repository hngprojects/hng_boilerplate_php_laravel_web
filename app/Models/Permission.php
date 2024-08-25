<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_permissions', 'permission_id', 'role_id')
                    ->using(RolePermission::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_permissions');
    }
}
