<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DesignResource extends JsonResource
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
            "id" => 1,
            "user" => new UserResource($this->user),
            "title" => $this->title,
            "description" => $this->description,
            "slug" => $this->slug,
            "images" => $this->images,
            "is_live" => $this->is_live,
            "disk" => $this->disk,
            "tag_lists" => [
                "tags" => $this->tagArray,
                "normalized" => $this->tagArrayNormalized,
            ],
            "created_at_dates" => [
                "created_at_human" => $this->created_at->diffForHumans(),
                "created_at" => $this->created_at,
            ],
            "updated_at_dates" => [
                "updated_at_human" => $this->updated_at->diffForHumans(),
                "updated_at" => $this->updated_at,
            ],
        ];
    }
}