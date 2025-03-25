<?php

namespace App\Http\Resources\Claim;

use Illuminate\Http\Resources\Json\JsonResource;

class ClaimCatalogueItemImagesResource extends JsonResource
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
            'main_large' => $this->getMainLarge(),
            'main_middle' => $this->getMainMiddle(),
            'main_small' => $this->getMainSmall(),
            'catalogue_image' => $this->getCatalogue(),
            'carousel' => $this->getCarousel(),
        ];
    }
}
