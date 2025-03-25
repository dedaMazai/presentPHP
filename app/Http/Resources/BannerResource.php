<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
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
            'place' => $this->place->value,
            'image' => (new ImageResource($this->image)),
            'news_id' => $this->news_id,
            'category_crm_id' => $this->category_crm_id,
            'url' => $this->url,
            'order' => $this->order,
        ];
    }
}
