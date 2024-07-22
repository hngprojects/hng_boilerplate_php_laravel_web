<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Organisation;

class OrganisationPolicy
{
    public function removeUser(User $user, Organisation $organisation)
    {
        // Check if the user is a superadmin
        if ($user->roles()->where('name', 'superadmin')->exists()) {
            return true;
        }

        // Check if the user is an organisation admin
        if ($user->roles()->where('org_id', $organisation->org_id)
                          ->where('name', 'admin')
                          ->exists()) {
            return true;
        }
        
        return false;

    }
}
