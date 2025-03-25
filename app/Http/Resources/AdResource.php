<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'place' => $this->place->value,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'news_id' => $this->news_id,
            'url' => $this->url,
            'image' => $this->image_id ? (new ImageResource($this->image)) : null,
        ];
    }
}
