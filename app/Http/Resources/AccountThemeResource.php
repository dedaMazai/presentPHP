<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountThemeResource extends JsonResource
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
            'theme_id' => $this->getThemeId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'image_url_market' => $this->getThemeId() == 4 ? $this->getMarketImageUrl() : null
        ];
    }
}
