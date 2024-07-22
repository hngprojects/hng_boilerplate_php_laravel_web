<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'feature' => $this->feature,
            'description' => $this->description,
            'status' => $this->pivot->status,
            'status_text' => $this->pivot->status ? 'active' : 'inactive'
        ];
    }
}
