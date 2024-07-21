<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRolesAndPermissions
{
    // get permission
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'users_permissions');
    }

    // get roles
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'users_roles');
    }

    // check if model has permission
    public function hasPermission($permission): bool
    {
        $name = "";

        if (is_string($permission)) {
            $name = $permission;
        }

        if ($permission instanceof Permission) {
            $name = $permission->name;
        }

        return $this->permissions()->where('name', $name)->count() > 0;
    }

    // check if model has permission through a role
    public function hasPermissionThroughRole($permission): bool
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        $roles = $this->roles();

        foreach ($permission->roles as $permission_role) {
            foreach ($roles as $role) {
                if ($role->name == $permission_role->name) {
                    return true;
                }
            }
        }

        return false;
    }

    // check if model has role
    public function hasRole($role): bool
    {
        $name = "";

        if (is_string($role)) {
            $name = $role;
        }

        if ($role instanceof Role) {
            $name = $role->name;
        }

        return $this->roles()->where('name', $name)->count() > 0;
    }

    // check if model has any role
    public function hasAnyRole(array $roles): bool
    {
        foreach($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    // check if model has any permission
    public function hasAnyPermission(array $permissions): bool
    {
        foreach($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    // give permissions
    public function givePermissionTo(...$permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        if (!$permissions) {
            return $this;
        }

        $this->permissions()->attach($permissions);
        return $this;
    }

    // revoke permissions
    public function revokePermissionTo(...$permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        if (!$permissions) {
            return $this;
        }

        $this->permissions()->detach($permissions);
        return $this;
    }

    // assign roles
    public function assignRole(...$roles)
    {
        $roles = $this->getAllRoles($roles);
        if (!$roles) {
            return $this;
        }

        $this->roles()->attach($roles);
        return $this;
    }

    // remove roles
    public function removeRole(...$roles)
    {
        $roles = $this->getAllRoles($roles);
        if (!$roles) {
            return $this;
        }

        $this->roles()->detach($roles);
        return $this;
    }

    protected function getAllPermissions(...$permissions)
    {
        return Permission::whereIn('name', $permissions)->get();
    }
}
