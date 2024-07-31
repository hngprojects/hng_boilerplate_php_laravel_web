<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'product_id' => $this->product_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'imageUrl' => $this->imageUrl,
            'sock' => $this->quantity,
            'date_added' => $this->created_at,
            'category' => $this->categories->isNotEmpty() ? $this->categories->map->name : [],

        ];
    }
}
