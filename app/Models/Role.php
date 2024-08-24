<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'description', 'org_id', 'is_active', 'is_default'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions', 'role_id', 'permission_id')
                    ->using(RolePermission::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_roles', 'role_id', 'user_id');
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'org_id', 'org_id');
    }
}
