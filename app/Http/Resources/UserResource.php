<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'email' => $this->email,
            'create_dates' => [
                'creadted_at_human' => $this->created_at->diffForHumans(),
                'creadted_at' => $this->created_at,
            ],
            'formatted_address' => $this->formatted_address,
            'tagline' => $this->tagline,
            'about' => $this->about,
            'location' => $this->location,
            'available_to_hire' => $this->available_to_hire,

        ];
    }
}
