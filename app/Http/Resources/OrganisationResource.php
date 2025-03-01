<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use \Illuminate\Http\Request;

class OrganisationResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->org_id,
            'name' => $this->name,
            'email' => $this->email,
            'description' => $this->description,
            'is_owner' => true,
            'role' => 'Admin',
            'industry' => $this->industry,
            'type' => $this->type,
            'country' => $this->country,
            'address' => $this->address,
            'state' => $this->state,
            'created_at' => $this->created_at,
        ];
    }
}
