<?php
namespace App\Services;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\User;
use Illuminate\Http\Request;

class OrganisationService 
{
    public function __construct()
    {
        
    }

    public function create(User $user, string $name): Organisation
    {
       $organisation = $user->owned_organisations()->create([
            'name' => $name
       ]);
       $this->assign_member($user, $organisation);
       return $organisation;
    }

    public function assign_member(User $user, Organisation $organisation) {
        OrganisationUser::create([
            'user_id' => $user->id,
            'org_id' => $organisation->org_id
        ]);
    }
}