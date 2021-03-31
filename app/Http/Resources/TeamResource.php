<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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
            'name' => $this->name,
            'total_members' => $this->members->count(),
            'slug' => $this->slug,
            "created_at_dates" => [
                "created_at_human" => $this->created_at->diffForHumans(),
                "created_at" => $this->created_at,
            ],
            "updated_at_dates" => [
                "updated_at_human" => $this->updated_at->diffForHumans(),
                "updated_at" => $this->updated_at,
            ],
            'designs' => DesignResource::collection($this->designs),
            'owner' => new UserResource($this->owner),
            'members' => UserResource::collection($this->members),
        ];
    }
}
