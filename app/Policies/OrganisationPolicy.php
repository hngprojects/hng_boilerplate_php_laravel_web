<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Organisation;

class OrganisationPolicy
{
    public function removeUser(User $user, Organisation $organisation)
    {
        // Check if the user is a superadmin
        if ($user->hasRole('superadmin')) {
            return true;
        }

        // Check if the user is an organisation admin
        if ($user->id === $organisation->user_id) {
            return true;
        } 
        
        return false;

    }
}
